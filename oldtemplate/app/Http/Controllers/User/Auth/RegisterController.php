<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{

    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        Intended::identifyRoute();
        return view('Template::user.auth.register', compact('pageTitle'));
    }


    protected function validator(array $data)
    {

        $passwordValidation = Password::min(6);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate     = Validator::make($data, [
            'full_name' => 'required|string|max:80',
            'email'     => 'required|string|email|max:40|unique:users',
            'mobile'    => 'required|string|max:40|regex:/^[0-9+().\-\s]+$/',
            'password'  => ['required', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ],[
            'full_name.required'=>'The full name field is required'
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }



        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }



    protected function create(array $data)
    {
        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

        [$firstname, $lastname] = $this->splitFullName($data['full_name']);
        $location = $this->defaultLocation();

        //User Create
        $user            = new User();
        $user->email     = strtolower($data['email']);
        $user->firstname = $firstname;
        $user->lastname  = $lastname;
        $user->username  = $this->makeUsername($data['email']);
        $user->mobile    = preg_replace('/\s+/', '', $data['mobile']);
        $user->dial_code = $location['dial_code'];
        $user->country_code = $location['country_code'];
        $user->country_name = $location['country_name'];
        $user->password  = Hash::make($data['password']);
        $user->ref_by    = $referUser ? $referUser->id : 0;
        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;
        $user->profile_complete = Status::YES;
        $user->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        \App\Services\TelegramService::notifyNewUser($user);


        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();


        return $user;
    }

    private function splitFullName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);
        return [$parts[0] ?? 'Client', $parts[1] ?? ''];
    }

    private function makeUsername(string $email): string
    {
        $base = strtolower(strtok($email, '@') ?: 'client');
        $base = preg_replace('/[^a-z0-9_]/', '', str_replace(['.', '-'], '_', $base));
        $base = trim(substr($base, 0, 24), '_') ?: 'client';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
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

    public function checkUser(Request $request){
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email',$request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile',$request->mobile)->where('dial_code',$request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username',$request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        return to_route('user.home');
    }

}
