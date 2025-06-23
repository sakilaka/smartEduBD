<?php

namespace App\Http\Controllers\Api\Guardian\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * login 
     *
     * @param Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);
        $requestFrom = app('request_from');

        $guardian = Guardian::where('mobile', $request->mobile)->first();

        if (!$guardian || !Hash::check($request->password, $guardian->password)) {
            throw ValidationException::withMessages([
                'logged' => 'The provided credentials are incorrect.',
            ]);
        }

        // set current student
        if (empty($guardian->current_student_id)) {
            $student = $guardian->students()->first();
            if (!empty($student)) {
                $guardian->update(['current_student_id' => $student['id']]);
            }
        }

        // get student profile
        $student = Student::with('profile:student_id,profile', 'academic_class', 'institution')
            ->select('id', 'academic_class_id', 'institution_id', 'software_id', 'name_en')
            ->find($guardian->current_student_id);

        // delete old tokens and create token
        $guardian->tokens()->where('abilities', 'like', "%$requestFrom%")->delete();

        $token = $guardian->createToken("guardian{$guardian->mobile}", ["role:guardian-{$requestFrom}"])->plainTextToken;
        $data = [
            "token_type" => "Bearer",
            'access_token' => $token,
            'user' => $guardian,
            'student' => $student
        ];

        return $this->sendResponse($data, 200, 'Login successfully');
    }

    /**
     * logout
     *
     * @param Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 200, 'Logout successfully');
    }

    /**
     * delete account
     *
     * @param Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        $request->user()->update(['status' => 'deactive']);
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 200, 'Account delete successfully');
    }
}
