<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Base\BaseController;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends BaseController
{
    /**
     * Get Logged Student Info
     *
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        $data = Student::with(
            'academic_class',
            'academic_session',
            'qualification',
            'department',
            'hostel'
        )->find(auth()->user()->id);
        return $this->sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $student = Student::find(auth()->user()->id);
        if ($this->validateCheck($request->all(), $student->id)) {
            $file = $request->file('profile');

            $arr = [
                'name'                     => strtoupper($request->name),
                'gender'                   => $request->gender,
                'fathers_name'             => strtoupper($request->fathers_name),
                'mothers_name'             => strtoupper($request->mothers_name),
                'readmission_college_roll' => $request->readmission_college_roll,
                'admission_id'             => $request->admission_id,
                'college_roll'             => $request->college_roll,
                'email'                    => $request->email,
                'address'                  => strtoupper($request->address),
                'reg_no'                   => $request->reg_no,
                'ssc_gpa'                  => $request->ssc_gpa,
                'hostel_id'                => $request->hostel_id,
                'hostel_room_no'           => $request->hostel_room_no,
                'blood_group'              => $request->blood_group,
                'nid'                      => $request->nid,
                'dob'                      => $request->dob,
                'religion'                 => $request->religion,
                'guardian_type'            => $request->guardian_type,
                'guardian_name'            => $request->guardian_name,
                'guardian_mobile'          => $request->guardian_mobile,
                'guardian_relations'       => $request->guardian_relations,
            ];

            // if (!empty($file)) {
            //     $this->oldFile($student->profile, true);
            //     $imagePath      = $this->resizer($file, ['student_profile']);
            //     $arr['profile'] = $imagePath['resize0'] ?? null;
            // }

            $student->update($arr);

            return $this->sendResponse([], 200, 'Information Update Successfully!');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function notices()
    {
        $query = Notice::latest('id')->limit(15);
        $query->whereSub('assignables', 'institution_id', auth()->user()->institution_id);
        $query->whereSub('assignables', 'academic_qualification_id', auth()->user()->academic_qualification_id);
        $query->whereSub('assignables', 'academic_class_id', auth()->user()->academic_class_id);
        $query->orWhere('all_class', 1)->where('type', 'student');

        $data = $query->get()->toArray();

        return $this->sendResponse($data);
    }

    /**
     * Change Password
     *
     * @param  \App\Student $student
     * @return \Illuminate\Http\Response
     */
    public function passwordChange(Request $request)
    {
        $authID   = auth()->user()->id;
        $old_pass = $request->old_password;
        if (!Hash::check($old_pass, auth()->user()->password)) {
            return $this->sendError("Sorry!! Old password doesn't match our records", 422);
        }

        $request->validate([
            'new_password'     => 'required|min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
        $password = Hash::make($request->new_password);
        Student::where('id', $authID)->update(['password' => $password]);
        return $this->sendResponse([], 200, "Password change successfully!!");
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($data)
    {
        return Validator::make($data, [
            "name"         => "required",
            "admission_id" => "required",
            "email"        => "nullable",
            "password"     => "required",
            "address"      => "required",
            "profile"      => "image|mimes:jpg,png,jpeg|mimetypes:image/jpeg,image/png",
        ]);
    }
}
