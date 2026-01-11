<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('admin.logout');
    }

    public function login(Request $request)
    {
        if ($request->method() == "GET") {
            return view('auth.login');
        }

        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'dial_code' => 'required|exists:users,dial_code',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('dial_code', 'phone_number', 'password'))) {
            $user = Auth::user();
        } else {
            return redirect()->to('admin.login')->with('error', 'Your credentials are invalid');
        }

        if($user->status == 0) {
            session()->flush();
            return redirect()->to('admin.login')->with('error', 'Your access to app has been blocked! Please contact the administrator');
        }
        
        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    protected function authenticated(Request $request, $user) 
    {
        if (Auth::check()) {
         return redirect()->route('dashboard');
        }
        return redirect()->intended();
    }

    public function logout(Request $request)
    {
        $savedAccounts = $request->session()->get('saved_accounts');

        auth()->guard('web')->logout();

        $request->session()->invalidate();

        if ($savedAccounts) {
            $request->session()->put('saved_accounts', $savedAccounts);
        }

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
