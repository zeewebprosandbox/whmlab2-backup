<?php

namespace App\DomainRegisters;

use App\Models\AdminNotification;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Porkbun
{
    public $url = 'https://api.porkbun.com/api/json/v3';
    public $domain;
    public $request;
    public $register;
    public $porkbunAcc;
    public $singleSearch;

    public function __construct($domain)
    {
        $this->domain = $domain;
        $this->register = DomainRegister::where('alias', 'Porkbun')->firstOrFail();
        $this->porkbunAcc = $this->register->params;
    }

    protected function api($endpoint, array $payload = [])
    {
        $apiKey = trim((string) ($this->porkbunAcc->api_key->value ?? ''));
        $secretApiKey = trim((string) ($this->porkbunAcc->secret_api_key->value ?? ''));

        if ($apiKey === '' || $secretApiKey === '') {
            return [
                'success' => false,
                'message' => 'Porkbun API key and secret API key are required. Add both credentials in Admin > Domain Registers.',
                'code' => 'MISSING_CREDENTIALS',
            ];
        }

        $response = Http::asJson()
            ->timeout(20)
            ->post($this->url.'/'.$endpoint, array_merge([
                'apikey' => $apiKey,
                'secretapikey' => $secretApiKey,
            ], $payload));

        $body = $response->json();
        if (!is_array($body)) {
            $body = [];
        }

        if (!$response->successful()) {
            $message = $body['message'] ?? 'Porkbun API responded with HTTP '.$response->status();
            $code = $body['code'] ?? 'HTTP_'.$response->status();
            $hint = data_get($body, 'next_action.hint');

            return [
                'success' => false,
                'message' => trim('Porkbun: '.$message.($hint ? ' '.$hint : '')),
                'code' => $code,
                'http_status' => $response->status(),
                'retry_after' => $body['ttlRemaining'] ?? $response->header('X-RateLimit-Reset'),
                'response' => $body,
            ];
        }

        if (($body['status'] ?? null) !== 'SUCCESS') {
            return [
                'success' => false,
                'message' => 'Porkbun: '.($body['message'] ?? $body['code'] ?? 'Porkbun API request failed'),
                'code' => $body['code'] ?? 'PORKBUN_ERROR',
                'response' => $body,
            ];
        }

        return ['success' => true, 'response' => $body, 'message' => 'OK'];
    }

    protected function makeNameservers($request, $domain, $noChange = false)
    {
        if ($request) {
            return array_values(array_filter([$request->ns1, $request->ns2, $request->ns3, $request->ns4]));
        }

        if ($noChange) {
            return array_values(array_filter([$domain->ns1, $domain->ns2, $domain->ns3, $domain->ns4]));
        }

        $server = @$domain->hosting->server;
        if ($server) {
            return array_values(array_filter([$server->ns1, $server->ns2, $server->ns3, $server->ns4]));
        }

        if ($domain->ns1 && $domain->ns2) {
            return array_values(array_filter([$domain->ns1, $domain->ns2, $domain->ns3, $domain->ns4]));
        }

        return array_values(array_filter([$this->register->ns1, $this->register->ns2, $this->register->ns3, $this->register->ns4]));
    }

    protected function checkDomain($domain, $priceType = null, $cacheSeconds = 0)
    {
        $payload = [];
        if ($priceType) {
            $payload['priceType'] = $priceType;
        }

        $callback = fn () => $this->api('domain/checkDomain/'.$domain, $payload);
        if (!$cacheSeconds) {
            return $callback();
        }

        return Cache::remember('porkbun.check.'.md5($domain.'|'.$priceType), $cacheSeconds, $callback);
    }

    protected function isRateLimitResponse(array $response)
    {
        return ($response['code'] ?? null) === 'RATE_LIMIT_EXCEEDED'
            || ($response['http_status'] ?? null) === 429;
    }

    protected function availabilityFromResponse(array $response)
    {
        $available = data_get($response, 'response.avail', data_get($response, 'avail'));

        return $available === 'yes' || $available === true;
    }

    protected function priceFromResponse(array $response)
    {
        return data_get($response, 'response.price')
            ?? data_get($response, 'response.registrationPrice')
            ?? data_get($response, 'price');
    }

    protected function priceCents($price)
    {
        $amount = (float) preg_replace('/[^0-9.]/', '', (string) $price);

        return (int) round($amount * 100);
    }

    protected function localContact()
    {
        $user = $this->domain->user;
        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = @$countryData[@$user->country_code]->dial_code ?: '';
        $phone = (string) $user->mobile;

        if ($dialCode && str_starts_with($phone, $dialCode)) {
            $phone = substr($phone, strlen($dialCode));
        }

        return (object) [
            'name' => $user->fullname,
            'address1' => $user->address,
            'address2' => $user->address,
            'city' => $user->city,
            'zip' => $user->zip,
            'country' => $user->country_code,
            'telnocc' => $dialCode,
            'telno' => $phone,
            'emailaddr' => $user->email,
        ];
    }

    public function register()
    {
        $domain = $this->domain;
        $nameservers = $this->makeNameservers($this->request, $domain);

        try {
            $availability = $this->checkDomain($domain->domain);
            if (!$availability['success']) {
                $this->adminNotification($domain, $availability['message']);
                return $availability;
            }

            if (!$this->availabilityFromResponse($availability['response'])) {
                return ['success' => false, 'message' => 'Domain is not available for registration'];
            }

            $cost = $this->priceCents($this->priceFromResponse($availability['response']));
            if (!$cost) {
                return ['success' => false, 'message' => 'Porkbun did not return a registration cost for this domain'];
            }

            $response = $this->api('domain/create/'.$domain->domain, [
                'cost' => $cost,
                'agreeToTerms' => 'yes',
                'whoisPrivacy' => $domain->id_protection ? 'yes' : 'no',
            ]);

            if (!$response['success']) {
                $this->adminNotification($domain, $response['message']);
                return $response;
            }

            if (count($nameservers) >= 2) {
                $nsResponse = $this->changeNameservers();
                if (!$nsResponse['success']) {
                    return $nsResponse;
                }
            }

            $domain->ns1 = $nameservers[0] ?? null;
            $domain->ns2 = $nameservers[1] ?? null;
            $domain->ns3 = $nameservers[2] ?? null;
            $domain->ns4 = $nameservers[3] ?? null;
            $domain->status = 1;
            $domain->save();

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function renew()
    {
        try {
            $availability = $this->checkDomain($this->domain->domain, 'renewal');
            if (!$availability['success']) {
                $this->adminNotification($this->domain, $availability['message']);
                return $availability;
            }

            $cost = $this->priceCents($this->priceFromResponse($availability['response']));
            if (!$cost) {
                return ['success' => false, 'message' => 'Porkbun did not return a renewal cost for this domain'];
            }

            $response = $this->api('domain/renew/'.$this->domain->domain, ['cost' => $cost]);
            if (!$response['success']) {
                $this->adminNotification($this->domain, $response['message']);
                return $response;
            }

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function getContact()
    {
        return ['success' => true, 'response' => $this->localContact()];
    }

    public function setContact()
    {
        return [
            'success' => false,
            'message' => 'Porkbun contact changes must be managed in Porkbun. Registration, renewal, availability, and nameserver automation are supported.',
        ];
    }

    public function changeNameservers()
    {
        $domain = $this->domain;
        $nameservers = $this->makeNameservers($this->request, $domain, true);

        if (count($nameservers) < 2) {
            return ['success' => false, 'message' => 'At least two nameservers are required'];
        }

        try {
            $response = $this->api('domain/updateNs/'.$domain->domain, [
                'ns' => $nameservers,
            ]);

            if (!$response['success']) {
                $this->adminNotification($domain, $response['message']);
                return $response;
            }

            $domain->ns1 = $nameservers[0] ?? null;
            $domain->ns2 = $nameservers[1] ?? null;
            $domain->ns3 = $nameservers[2] ?? null;
            $domain->ns4 = $nameservers[3] ?? null;
            $domain->save();

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function enableIdProtection()
    {
        $this->domain->id_protection = 1;
        $this->domain->save();

        return ['success' => true, 'message' => 'Porkbun WHOIS privacy will be requested on new registrations'];
    }

    public function disableIdProtection()
    {
        $this->domain->id_protection = 0;
        $this->domain->save();

        return ['success' => true, 'message' => 'Porkbun WHOIS privacy will not be requested on new registrations'];
    }

    protected function adminNotification($data, $message)
    {
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user_id;
        $adminNotification->title = gettype($message) == 'array' ? implode('. ', $message) : $message;
        $adminNotification->api_response = 1;
        $adminNotification->click_url = urlPath('admin.order.domain.details', $data->id);
        $adminNotification->save();
    }

    public function searchDomain()
    {
        $domain = $this->domain;
        $isSupported = true;

        $domainSetup = DomainSetup::active()->with('pricing')->get(['id', 'extension']);
        $requestedTld = \getTld($domain);
        if ($requestedTld && !$domainSetup->where('extension', $requestedTld)->first()) {
            $isSupported = false;
        }

        $domain = function_exists('normalizeDomainSearchDomain') ? \normalizeDomainSearchDomain($domain, $domainSetup) : $domain;
        $sld = function_exists('getSld') ? \getSld($domain) : explode('.', $domain)[0];
        $tld = function_exists('getTld') ? \getTld($domain) : (str_contains($domain, '.') ? '.' . explode('.', $domain, 2)[1] : '.com');
        $liveSearch = $this->request && $this->request->boolean('live');
        $limit = 1;
        $searchDomains = function_exists('domainSearchCandidates') ? \domainSearchCandidates($this->domain, $domainSetup, $this->singleSearch, $limit) : [$domain];

        try {
            if ($this->singleSearch) {
                $response = $this->checkDomain($domain, null, 300);
                if (!$response['success']) {
                    return $response;
                }

                return [
                    'success' => true,
                    'regster_name' => 'porkbun',
                    'domain' => $domain,
                    'sld' => $sld,
                    'tld' => $tld,
                    'isSupported' => $isSupported,
                    'setup' => $domainSetup->where('extension', $tld)->first(),
                    'data' => [
                        $domain => [
                            'status' => $this->availabilityFromResponse($response['response']) ? 'available' : 'unavailable',
                        ],
                    ],
                ];
            }

            $result = [];
            $deferred = false;
            foreach ($searchDomains as $dataDomain) {
                $dataTld = getTld($dataDomain);
                $response = $this->checkDomain($dataDomain, null, 300);

                if (!$response['success']) {
                    if ($this->isRateLimitResponse($response) && $liveSearch) {
                        $deferred = true;
                        break;
                    }

                    return $response;
                }

                $result[] = [
                    'match' => $domain == $dataDomain ? 999 : 0,
                    'domain' => $dataDomain,
                    'setup' => $domainSetup->where('extension', $dataTld)->first(),
                    'available' => $this->availabilityFromResponse($response['response']),
                ];
            }

            return [
                'success' => true,
                'regster_name' => 'porkbun',
                'domain' => $domain,
                'sld' => $sld,
                'tld' => $tld,
                'isSupported' => $isSupported,
                'deferred' => $deferred,
                'message' => $deferred ? 'Porkbun is rate-limited for live checks. Pause briefly or press Search for a full check.' : null,
                'data' => $result,
            ];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }
}
