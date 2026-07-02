<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Domain;
use App\Models\Form;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\SupportPin;
use App\Models\SupportTicket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = auth()->user();
        $totalTicket = SupportTicket::where('user_id', $user->id)->count();
        $totalInvoice = Invoice::where('user_id', $user->id)->count();
        $totalDomain = Domain::where('user_id', $user->id)->count();
        $totalService = Hosting::where('user_id', $user->id)->count();
        $totalOverDueInvoice = Invoice::unpaid()->where('user_id', $user->id)->selectRaw('count(*) as total, sum(amount) as totalDue')->first();
        $supportPin = $this->activeSupportPin($user);

        return view('Template::user.dashboard', compact('pageTitle', 'user', 'totalTicket', 'totalInvoice', 'totalDomain', 'totalService', 'totalOverDueInvoice', 'supportPin'));
    }

    public function regenerateSupportPin()
    {
        $user = auth()->user();
        SupportPin::where('user_id', $user->id)->active()->update(['used_at' => now()]);

        $supportPin = $this->createSupportPin($user);

        $notify[] = ['success', 'Support PIN regenerated. Share it only with verified support staff.'];
        return back()->withNotify($notify);
    }

    public function depositHistory(Request $request)
    {
        if(!gs('deposit_module')){
            return abort(404);
        }

        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::where('remark', '!=', null)->distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id',auth()->id())->searchable(['trx'])->filter(['trx_type','remark'])->orderBy('id','desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle','transactions','remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error','Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error','You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act','kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle','form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        abort_if($user->kv == Status::VERIFIED,403);
        return view('Template::user.kyc.info', compact('pageTitle','user'));
    }

    public function kycSubmit(Request $request)
    {  
        $form = Form::where('act','kyc')->first();
        $formData = @$form->form_data ?? [];
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify').'/'.$kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);
        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success','KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'Complete Your Profile';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $request->validate([
            'mobile' => ['required','string','max:40','regex:/^[0-9+().\-\s]+$/',Rule::unique('users')->ignore($user->id)],
        ]);

        if (!$user->username) {
            $user->username = $this->makeUsername($user->email);
        }

        if (!$user->country_code || !$user->country_name || !$user->dial_code) {
            $location = $this->defaultLocation();
            $user->country_code = $user->country_code ?: $location['country_code'];
            $user->country_name = $user->country_name ?: $location['country_name'];
            $user->dial_code = $user->dial_code ?: $location['dial_code'];
        }

        $user->mobile = preg_replace('/\s+/', '', $request->mobile);
        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }

    private function makeUsername(string $email): string
    {
        $base = strtolower(strtok($email, '@') ?: 'client');
        $base = preg_replace('/[^a-z0-9_]/', '', str_replace(['.', '-'], '_', $base));
        $base = trim(substr($base, 0, 24), '_') ?: 'client';
        $username = $base;
        $counter = 1;

        while (\App\Models\User::where('username', $username)->exists()) {
            $suffix = $counter++;
            $username = substr($base, 0, 24 - strlen((string) $suffix)) . $suffix;
        }

        return $username;
    }

    private function defaultLocation(): array
    {
        $info = json_decode(json_encode(getIpInfo()), true);
        $countryCode = @implode(',', $info['code']) ?: null;
        $countryName = @implode(',', $info['country']) ?: null;
        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = $countryCode && isset($countryData->{$countryCode}) ? $countryData->{$countryCode}->dial_code : null;

        return [
            'country_code' => $countryCode,
            'country_name' => $countryName,
            'dial_code' => $dialCode,
        ];
    }

    private function activeSupportPin($user): SupportPin
    {
        $supportPin = SupportPin::where('user_id', $user->id)->active()->latest()->first();

        if (!$supportPin) {
            return $this->createSupportPin($user);
        }

        $supportPin->plain_code = Crypt::decryptString($supportPin->code_encrypted);

        return $supportPin;
    }

    private function createSupportPin($user): SupportPin
    {
        $code = (string) random_int(100000, 999999);

        $supportPin = new SupportPin();
        $supportPin->user_id = $user->id;
        $supportPin->code_hash = Hash::make($code);
        $supportPin->code_encrypted = Crypt::encryptString($code);
        $supportPin->expires_at = now()->addMinutes(10);
        $supportPin->save();

        $supportPin->plain_code = $code;

        return $supportPin;
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')).'- attachments.'.$extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error','File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function emailHistory(Request $request){   
        $pageTitle = 'Email History';
        $emails = NotificationLog::where('user_id', auth()->user()->id)->where('notification_type', 'email')->with('user')->orderBy('id','desc')->paginate(getPaginate());
        return view('Template::user.email_history', compact('pageTitle', 'emails'));
    }

    function emailDetails($id){ 
        $pageTitle = 'Email Details'; 
        $email = NotificationLog::where('user_id', auth()->user()->id)->where('notification_type', 'email')->findOrFail($id);
        return view('Template::user.email_details', compact('pageTitle', 'email'));
    }

    public function loginHosting($id){
        
        $service = Hosting::whereBelongsTo(auth()->user())->findOrFail($id);
        $product = $service->product;
        $server = $service->server;
        $serverGroup = $server->group;

        if(!$product->server_group_id){
            $notify[] = ['error', 'Unable to auto-login'];
            return back()->withNotify($notify);
        }

        if(!$server){
            $notify[] = ['error', 'There is no selected server to auto-login'];
            return back()->withNotify($notify); 
        }

        $execute = HostingManager::init($serverGroup)->loginAccount($service);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        if (empty($execute['url'])) {
            $notify[] = ['error', 'ZodPanel did not return an auto-login URL'];
            return back()->withNotify($notify);
        }

        return redirect()->away($execute['url']);
    }
}
