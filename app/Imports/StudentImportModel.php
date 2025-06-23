<?php

namespace App\Imports;

use App\Models\Student;
use App\Helpers\GlobalHelper;
use App\Traits\SMSTrait;
use App\Models\Guardian;
use App\Models\StudentProfile;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImportModel implements ToModel, WithHeadingRow
{
    use SMSTrait;

    protected $data = [];
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $exists_arr = [];
        // dd($this->data, $row);
        $password  = '123456';
        // $password  = rand(111111, 999999);

        if (empty($row['guardian_mobile_no']) && empty($row['guardian_name_en'])) {
            return null;
        }

        // create guardian
        $guardianData = [
            'type'      => $row['guardian_type'],
            'relations' => $row['guardian_type'],
            'name_en'   => $row['guardian_name_en'],
            'name_bn'   => $row['guardian_name_bn'],
            'mobile'    => $row['guardian_mobile_no'],
            'nid_or_birth_reg' => $row['guardian_nid_brid'],
            'password'  => $password,
        ];
        $guardian = Guardian::firstOrCreate(['mobile' =>  $guardianData['mobile']], $guardianData);
        if (empty($guardian)) {
            array_push($exists_arr, $row);
            return null;
        }

        // create student
        $software_id = GlobalHelper::generate_id(Student::class, 'software_id', [
            'conditions' => ['institution_id' => $this->data['institution_id']],
            'pad_length' => 5,
            'prefix'     => $this->data['institution_id'],
        ]);

        // check students
        $exists = StudentProfile::where('nid_or_birth_reg', $row['brid'])->exists();
        if ($exists) {
            array_push($exists_arr, $row);
            return false;
        }

        if (empty($row['name_en'])) {
            return null;
        }

        $studentData = [
            'guardian_id'   => $guardian->id,
            'name_en'       => strtoupper($row['student_name_en']),
            'name_bn'       => $row['student_name_bn'],
            'mobile'        => $row['student_mobile_no'],
            'software_id'   => $software_id,
            'status'        => 'active',
            'password'      => $password,
            'created_from'  => 'excel',
        ];
        $data = $this->data + $studentData;

        $student = Student::create($data);

        // create profile
        $profileData = [
            'roll_number'       => $row['roll'],
            'nid_or_birth_reg'  => $row['brid'],
            'dob' => !empty($row['date_of_birth']) ? date('Y-m-d', strtotime($row['date_of_birth'])) : null,
            'gender'            => $row['gender'],
            'religion'          => $row['religion'],
            'disability'        => $row['disability_status'],
            'fathers_name_en'   => strtoupper($row['father_name_en']),
            'fathers_name_bn'   => $row['father_name_bn'],
            'fathers_mobile'    => $row['father_mobile_no'],
            'fathers_nid_or_birth_reg' => $row['father_nid_brid'],
            'mothers_name_en'   => strtoupper($row['mother_name_en']),
            'mothers_name_bn'   => $row['mother_name_bn'],
            'mothers_mobile'    => $row['mother_mobile_no'],
            'mothers_nid_or_birth_reg' => $row['mother_nid_brid'],
        ];
        $student->profile()->create($profileData);

        // Send SMS
        if ($student) {
            // $this->sendSms($student->mobile, 'StudentCreate', [], $student);
        }
    }
}
