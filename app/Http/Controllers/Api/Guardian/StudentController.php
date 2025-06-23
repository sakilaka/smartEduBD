<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentController extends Controller
{
    use ImageUpload;

    // for image upload
    protected $withDateFolder = true;
    protected $resize = [300, 300];

    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Students lists
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['current_student_id'] = auth()->user()->current_student_id;

        $data['students'] = auth()->user()
            ->students()
            ->with([
                'profile:student_id,profile',
                'academic_class',
                'institution' 
            ])
            ->select('id', 'academic_class_id', 'institution_id', 'software_id', 'name_en')
            ->get();

        return $this->sendResponse($data);
    }


    /**
     * Switch student
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function switchStudent(Request $request)
    {
        $student = auth()->user()->students()->where('id', $request->student_id)->first();
        throw_unless($student, NotFoundHttpException::class, 'No student found to change.');

        auth()->user()->update(['current_student_id' => $student->id]);

        $data = auth()->user()->loggedProfile();

        return $this->sendResponse($data, 200, 'Student switch successfully!');
    }

    /**
     * Get student profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $studentID = auth()->user()->current_student_id;
        $data = $this->studentService->profile($studentID);

        return $this->sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student $student
     * @return \Illuminate\Http\Response
     */
    public function update(StudentRequest $request)
    {
        try {
            DB::beginTransaction();
            $studentID = auth()->user()->current_student_id;

            $data = $request->validated();
            $profileData = $data['profile'];
            unset($data['profile']);

            $data['name_en'] = strtoupper($request->name_en);

            $student = Student::find($studentID);
            $student->update($data);

            // update profile
            if ($request->hasFile('image')) {
                $institutionName = Str::slug(auth()->user()->current_student->institution->short_name ?? '');
                $oldProfile = $student->profile->profile ?? '';
                $path = "{$institutionName}/student-profile";
                $profileData['profile'] = $this->upload($request->file('image'), $path, $oldProfile);
            }

            $profileData['fathers_name_en'] = strtoupper($profileData['fathers_name_en']);
            $profileData['mothers_name_en'] = strtoupper($profileData['mothers_name_en']);

            $student->profile()->update($profileData);

            DB::commit();
            return $this->sendResponse([], 200, 'Information Update Successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}