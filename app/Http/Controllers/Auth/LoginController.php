<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = 'user.dashboard';

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('layouts.frontend_app');
    }

    public function login(Request $request)
    {
        if ($this->validateCheck($request)) {
            $email = $request->email;
            $arr   = [
                'email'    => $email,
                'password' => $request->password,
            ];

            $admin = User::where('email', $email)->first();
            if (!empty($admin)) {
                if (Auth::attempt($arr, $request->remember)) {
                    return response([
                        'message' => "Login Successfully",
                        'id'      => Auth::user()->id ?? "",
                        'name'    => Auth::user()->name ?? "",
                    ], 200);
                } else {
                    return response([
                        'message' => "Your Login Information is Wrong!",
                    ], 201);
                }
            } else {
                return response([
                    'message' => "Email does not match our records!",
                ], 201);
            }
        }
    }

    /**
     * Validation check====
     */
    public function validateCheck($request)
    {
        return $request->validate([
            "email"    => "required",
            "password" => "required",
        ]);
    }

    public function loginCheck()
    {
        if (Auth::user()) {
            return response([
                'message' => "Login Successfully",
                'id'      => Auth::user()->id ?? "",
                'name'    => Auth::user()->name ?? "",
            ], 200);
        }
        return response([
            'message' => "Unauthorized",
        ], 201);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'loginCheck');
    }
}
