<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'admin';

    protected $maxAttempts = 5;

    protected $decayMinutes = 15;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Admin Login";
        return view('admin.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {
        return [
            $this->username() => $request->input($this->username()),
            'password' => $request->input('password'),
            'status' => 1,
        ];
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }




        $redirect = $request->redirect ?? null;
        if ($redirect) {

            try {
                $validUrl = null;
                $routes = \Route::getRoutes();

                $redirect = trim(str_replace(['..', '\\'], '', $redirect), '/');

                if (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://')) {
                    $redirect = null;
                }

                if ($redirect && @explode('/', @$redirect)[0] != 'admin') {
                    $redirect = 'admin' . '/' . $redirect;
                }

                if ($redirect) {
                    $requestRedirectUrl = Request::create($redirect);
                    $routes->match($requestRedirectUrl);
                    $validUrl = true;
                }

                if ($validUrl) {
                    $this->redirectTo = $redirect;
                }
            } catch (\Exception $e) {
                $validUrl = false;
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        if ((int) $user->status !== 1) {
            $this->guard()->logout();
            throw ValidationException::withMessages([
                $this->username() => [trans('Your admin account is disabled.')],
            ]);
        }

        $request->session()->regenerate();
        $request->session()->regenerateToken();
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }
}
