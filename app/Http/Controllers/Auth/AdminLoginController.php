<?php

/**
 * Dev: @Oshit Sutra Dhar
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout', 'loginCheck');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                "email"    => "required",
                "password" => "required",
            ]);

            $email = $request->email;
            $arr   = [
                'email'    => $email,
                'password' => $request->password,
                'status'   => 'active',
            ];

            $admin = Admin::where('email', $email)->first();
            if (!empty($admin)) {
                if ($admin->block == 0) {
                    if (Auth::guard('admin')->attempt($arr, $request->remember)) {
                        Session::forget($email);
                        Artisan::call('cache:clear');
                        $user = Auth::guard('admin')->user();

                        $data = [
                            'id'        => $user->id,
                            'name'      => $user->name,
                            'role_id'   => $user->role_id,
                            'role_name' => $user->role->name ?? '',
                            'institution_id' => $user->institution_id,
                            'institution_name' => $user->institution->name ?? '',
                            'institution_logo' => $user->institution->logo ?? '',
                        ];

                        // set institution in session
                        if (!empty($user->institution)) {
                            $institution = $user->institution()->select('id', 'name', 'logo')->first()->toArray();
                            session()->put("institution_{$user->id}", $institution);
                        }

                        return $this->sendResponse($data, 200, "Login Successfully");
                    } else {
                        $attempt = $this->loginAttempt($admin->id, $email);
                        if ($attempt <= 3 && $attempt != 0) {
                            $msg = "There are " . $attempt . " attempts left";
                        } else if ($attempt == 0) {
                            $msg = "Your account is block, please contact your administrator";
                        } else {
                            $msg = "Your Login Information is Wrong";
                        }
                        throw ValidationException::withMessages([$msg]);
                    }
                } else {
                    Session::forget($email);
                    throw ValidationException::withMessages(['Your account is block, please contact your administrator']);
                }
            } else {

                throw ValidationException::withMessages(['Email does not match our records!']);
            }
        } else if ($request->isMethod('get')) {
            return view('auth.admin-login');
        } else {
            return view('auth.admin-login');
        }
    }

    /**
     * Login Attempt
     * 
     * @param $id
     * @param $email
     */
    private function loginAttempt($id, $email)
    {
        $count = Session::get($email) ?? 0;
        $inc   = $count + 1;
        Session::put($email, $inc);

        $attempt = 6 - $inc;
        if ($attempt == 0) {
            Session::forget($email);
            Admin::where('id', $id)->update(['block' => 1]);
        }
        return $attempt;
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Artisan::call('cache:clear');

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->sendResponse([], 200, "Logout Successfully");
    }

    /**
     * Login check
     *
     * @return array
     */
    public function loginCheck()
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();

            $data = [
                'id'        => $user->id,
                'name'      => $user->name,
                'role_id'   => $user->role_id,
                'role_name' => $user->role->name ?? '',
                'institution_id' => $user->institution_id,
                'institution_name' => $user->institution->name ?? '',
                'institution_logo' => $user->institution->logo ?? '',
            ];

            return $this->sendResponse($data, 200, "Login Successfully");
        }

        throw ValidationException::withMessages(['Unauthorized']);
    }
}