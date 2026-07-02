<?php

namespace App\DomainRegisters;

use App\Models\AdminNotification;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use Illuminate\Support\Facades\Http;

class NameSilo
{
    public $url;
    public $domain;
    public $request;
    public $register;
    public $nameSiloAcc;
    public $singleSearch;

    public function __construct($domain)
    {
        $this->domain = $domain;
        $this->register = DomainRegister::where('alias', 'NameSilo')->firstOrFail();
        $this->url = $this->register->test_mode ? 'http://sandbox.namesilo.com/api/' : 'https://www.namesilo.com/api/';
        $this->nameSiloAcc = $this->register->params;
    }

    protected function api($operation, array $params = [])
    {
        $response = Http::get($this->url.$operation, array_merge([
            'version' => 1,
            'type' => 'xml',
            'key' => $this->nameSiloAcc->api_key->value,
        ], $params));

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'NameSilo API responded with HTTP '.$response->status()];
        }

        $data = xmlToArray($response->body());
        $reply = $data['reply'] ?? [];
        $code = (int)($reply['code'] ?? 0);

        if (!in_array($code, [300, 301, 302], true)) {
            return [
                'success' => false,
                'message' => $reply['detail'] ?? $reply['message'] ?? 'NameSilo API request failed',
                'response' => $reply,
            ];
        }

        return ['success' => true, 'response' => $reply, 'message' => $reply['detail'] ?? 'OK'];
    }

    protected function makeNameservers($request, $domain, $noChange = false)
    {
        $nameservers = null;
        $server = @$domain->hosting->server;

        if ($request) {
            $nameservers = array_filter([$request->ns1, $request->ns2, $request->ns3, $request->ns4]);
            return implode(',', $nameservers);
        }

        if ($noChange) {
            $nameservers = array_filter([$domain->ns1, $domain->ns2, $domain->ns3, $domain->ns4]);
            return implode(',', $nameservers);
        }

        if (@$server) {
            $nameservers = array_filter([$server->ns1, $server->ns2, $server->ns3, $server->ns4]);
            return implode(',', $nameservers);
        }

        if ($domain->ns1 && $domain->ns2) {
            $nameservers = array_filter([$domain->ns1, $domain->ns2, $domain->ns3, $domain->ns4]);
            return implode(',', $nameservers);
        }

        $nameservers = array_filter([$this->register->ns1, $this->register->ns2, $this->register->ns3, $this->register->ns4]);
        return implode(',', $nameservers);
    }

    protected function splitName($name)
    {
        $parts = preg_split('/\s+/', trim($name ?: 'Domain Owner'), 2);

        return [
            $parts[0] ?? 'Domain',
            $parts[1] ?? 'Owner',
        ];
    }

    protected function contactObjectFromUser($user)
    {
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

    protected function normalizeContact($contact)
    {
        $phone = $contact['phone'] ?? '';
        $phoneParts = explode('.', ltrim($phone, '+'), 2);

        return (object) [
            'name' => trim(($contact['first_name'] ?? '').' '.($contact['last_name'] ?? '')),
            'address1' => $contact['address'] ?? '',
            'address2' => $contact['address'] ?? '',
            'city' => $contact['city'] ?? '',
            'zip' => $contact['zip'] ?? '',
            'country' => $contact['country'] ?? '',
            'telnocc' => $phoneParts[0] ?? '',
            'telno' => $phoneParts[1] ?? ($phoneParts[0] ?? ''),
            'emailaddr' => $contact['email'] ?? '',
        ];
    }

    protected function resolveContactId()
    {
        if ($this->domain->contact_id) {
            return $this->domain->contact_id;
        }

        $response = $this->api('getDomainInfo', ['domain' => $this->domain->domain]);
        if (!$response['success']) {
            return null;
        }

        $contactId = data_get($response['response'], 'contact_ids.registrant');
        if (!$contactId) {
            return null;
        }

        $this->domain->contact_id = is_array($contactId) ? reset($contactId) : $contactId;
        $this->domain->save();

        return $this->domain->contact_id;
    }

    protected function createContactFromRequest()
    {
        [$firstName, $lastName] = $this->splitName($this->request->name);
        $phone = '+'.$this->request->telephonecc.'.'.$this->request->telephone;

        $response = $this->api('contactAdd', [
            'fn' => $firstName,
            'ln' => $lastName,
            'ad' => $this->request->address1,
            'cy' => $this->request->city,
            'st' => $this->request->state ?? 'N/A',
            'zp' => $this->request->zip,
            'ct' => $this->request->country,
            'em' => $this->request->email,
            'ph' => $phone,
        ]);

        if (!$response['success']) {
            return $response;
        }

        $contactId = data_get($response['response'], 'contact_id');
        if ($contactId) {
            $this->domain->contact_id = is_array($contactId) ? reset($contactId) : $contactId;
            $this->domain->save();
        }

        return $response;
    }

    public function register()
    {
        $domain = $this->domain;
        $nameservers = $this->makeNameservers($this->request, $domain);
        $array = array_values(array_filter(explode(',', $nameservers)));

        try {
            $response = $this->api('registerDomain', [
                'domain' => $domain->domain,
                'years' => $domain->reg_period,
                'private' => $domain->id_protection ? 1 : 0,
                'auto_renew' => 0,
            ]);

            if (!$response['success']) {
                $this->adminNotification($domain, $response['message']);
                return $response;
            }

            if (count($array) >= 2) {
                $nsResponse = $this->changeNameservers();
                if (!$nsResponse['success']) {
                    return $nsResponse;
                }
            }

            $domain->ns1 = $array[0] ?? null;
            $domain->ns2 = $array[1] ?? null;
            $domain->ns3 = $array[2] ?? null;
            $domain->ns4 = $array[3] ?? null;
            $domain->status = 1;
            $domain->save();

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function renew()
    {
        $renewYear = $this->request ? $this->request->renew_year : $this->domain->reg_period;

        try {
            $response = $this->api('renewDomain', [
                'domain' => $this->domain->domain,
                'years' => $renewYear,
            ]);

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
        try {
            $contactId = $this->resolveContactId();
            if (!$contactId) {
                return ['success' => true, 'response' => $this->contactObjectFromUser($this->domain->user)];
            }

            $response = $this->api('contactList');
            if (!$response['success']) {
                return $response;
            }

            $contacts = data_get($response['response'], 'contact', []);
            if (isset($contacts['contact_id'])) {
                $contacts = [$contacts];
            }

            foreach ($contacts as $contact) {
                if ((string)($contact['contact_id'] ?? '') === (string)$contactId) {
                    return ['success' => true, 'response' => $this->normalizeContact($contact)];
                }
            }

            return ['success' => true, 'response' => $this->contactObjectFromUser($this->domain->user)];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function setContact()
    {
        try {
            $contactId = $this->resolveContactId();
            if (!$contactId) {
                $created = $this->createContactFromRequest();
                if (!$created['success']) {
                    return $created;
                }

                $contactId = $this->domain->contact_id;
            }

            [$firstName, $lastName] = $this->splitName($this->request->name);
            $phone = '+'.$this->request->telephonecc.'.'.$this->request->telephone;

            $response = $this->api('contactUpdate', [
                'contact_id' => $contactId,
                'fn' => trim($firstName.' '.$lastName),
                'ad' => $this->request->address1,
                'cy' => $this->request->city,
                'st' => $this->request->state ?? 'N/A',
                'zp' => $this->request->zip,
                'ct' => $this->request->country,
                'em' => $this->request->email,
                'ph' => $phone,
            ]);

            if (!$response['success']) {
                $this->adminNotification($this->domain, $response['message']);
                return $response;
            }

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function changeNameservers()
    {
        $domain = $this->domain;
        $array = array_values(array_filter(explode(',', $this->makeNameservers($this->request, $domain, true))));

        if (count($array) < 2) {
            return ['success' => false, 'message' => 'At least two nameservers are required'];
        }

        try {
            $params = ['domain' => $domain->domain];
            foreach ($array as $index => $nameserver) {
                $params['ns'.($index + 1)] = $nameserver;
            }

            $response = $this->api('changeNameServers', $params);
            if (!$response['success']) {
                $this->adminNotification($domain, $response['message']);
                return $response;
            }

            $domain->ns1 = $array[0] ?? null;
            $domain->ns2 = $array[1] ?? null;
            $domain->ns3 = $array[2] ?? null;
            $domain->ns4 = $array[3] ?? null;
            $domain->save();

            return ['success' => true, 'message' => 'OK'];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }

    public function enableIdProtection()
    {
        $response = $this->api('addPrivacy', ['domain' => $this->domain->domain]);
        if (!$response['success']) {
            return $response;
        }

        $this->domain->id_protection = 1;
        $this->domain->save();

        return ['success' => true, 'message' => 'OK'];
    }

    public function disableIdProtection()
    {
        $response = $this->api('removePrivacy', ['domain' => $this->domain->domain]);
        if (!$response['success']) {
            return $response;
        }

        $this->domain->id_protection = 0;
        $this->domain->save();

        return ['success' => true, 'message' => 'OK'];
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

    protected function normalizeDomainList($value)
    {
        if (!$value) {
            return [];
        }

        if (is_string($value)) {
            return [$value];
        }

        if (isset($value['domain'])) {
            return $this->normalizeDomainList($value['domain']);
        }

        return array_map(function ($item) {
            return is_array($item) ? ($item['domain'] ?? reset($item)) : $item;
        }, $value);
    }

    public function searchDomain()
    {
        $domain = $this->domain;
        $sld = getSld($domain);
        $tld = getTld($domain);
        $isSupported = true;

        $domainSetup = DomainSetup::active()->with('pricing')->get(['id', 'extension']);
        if ($tld && !$domainSetup->where('extension', $tld)->first()) {
            $isSupported = false;
        }

        $searchDomains = [];
        if ($this->singleSearch) {
            $searchDomains[] = $domain;
        } else {
            foreach ($domainSetup as $setup) {
                $searchDomains[] = $sld.$setup->extension;
            }
        }

        try {
            $response = $this->api('checkRegisterAvailability', [
                'domains' => implode(',', $searchDomains),
            ]);

            if (!$response['success']) {
                return $response;
            }

            $available = $this->normalizeDomainList(data_get($response['response'], 'available.domain'));
            $unavailable = $this->normalizeDomainList(data_get($response['response'], 'unavailable.domain'));

            if ($this->singleSearch) {
                $getSetup = $domainSetup->where('extension', $tld)->first();

                return [
                    'success' => true,
                    'regster_name' => 'namesilo',
                    'domain' => $domain,
                    'sld' => $sld,
                    'tld' => $tld,
                    'isSupported' => $isSupported,
                    'setup' => $getSetup,
                    'data' => [
                        $domain => [
                            'status' => in_array($domain, $available, true) ? 'available' : 'unavailable',
                        ],
                    ],
                ];
            }

            $result = [];
            foreach ($searchDomains as $index => $dataDomain) {
                $dataTld = getTld($dataDomain);
                $getSetup = $domainSetup->where('extension', $dataTld)->first();

                $result[] = [
                    'match' => $domain == $dataDomain ? 999 : 0,
                    'domain' => $dataDomain,
                    'setup' => $getSetup,
                    'available' => in_array($dataDomain, $available, true) && !in_array($dataDomain, $unavailable, true),
                ];
            }

            return [
                'success' => true,
                'regster_name' => 'namesilo',
                'domain' => $domain,
                'sld' => $sld,
                'tld' => $tld,
                'isSupported' => $isSupported,
                'data' => $result,
            ];
        } catch (\Exception $error) {
            return ['success' => false, 'message' => $error->getMessage()];
        }
    }
}
