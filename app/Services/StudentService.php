<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Student;

class StudentService
{
    /**
     * logged student information
     *
     * @return \Illuminate\Http\Response
     */
    public function profile($id)
    {
        return Student::with(
            'profile',
            'institution',
            'guardian',
            'campus',
            'shift',
            'medium',
            'academic_class',
            'group',
            'section',
        )->find($id);
    }

    /**
     * Change auth user password
     *
     * @return \Illuminate\Http\Response
     */
    public function passwordChanged($model, $data)
    {
        $modelObj = new $model;

        if (!Hash::check($data['current_password'], auth()->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => "Current password doesn't match",
            ]);
        }

        $user = $modelObj->where('id', auth()->id())->first();
        return $user->update(['password' => $data['password']]);
    }
}
