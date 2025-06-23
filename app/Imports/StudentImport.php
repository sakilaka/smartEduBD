<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Helpers\GlobalHelper;
use App\Traits\SMSTrait;
use App\Models\Guardian;
use App\Models\StudentProfile;
use Illuminate\Support\Str;

class StudentImport implements ToCollection
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
    public function collection(Collection $rows)
    {
        $exists_arr = [];
        foreach ($rows as $key => $row) {
            if ($key == 0) {
                continue;
            }

            $password  = 'abc123';
            // $password  = rand(111111, 999999);

            if (empty($row[20]) || empty($row[18]) || empty($row[1])) {
                continue;
            }

            $gMobile = $row[20] ?? '';
            if (!Str::startsWith($gMobile, '0')) {
                $gMobile = '0' . $gMobile;
            }

            // create guardian
            $guardianData = [
                'type'      => $row[17],
                'relations' => $row[17],
                'name_en'   => $row[18],
                'name_bn'   => $row[19] ?? '',
                'mobile'    => $gMobile,
                'nid_or_birth_reg' => $row[21] ?? '',
                'password'  => $password,
            ];
            $guardian = Guardian::firstOrCreate(['mobile' =>  $guardianData['mobile']], $guardianData);
            if (empty($guardian)) {
                array_push($exists_arr, $row);
                continue;
            }

            // create student
            $software_id = GlobalHelper::generate_id(Student::class, 'software_id', [
                'conditions' => ['institution_id' => $this->data['institution_id']],
                'pad_length' => 5,
                'prefix'     => $this->data['institution_id'],
            ]);

            // check students
            $exists = StudentProfile::where('nid_or_birth_reg', $row[4])->exists();
            if ($exists) {
                array_push($exists_arr, $row);
                continue;
            }

            $sMobile = $row[3] ?? '';
            if (!Str::startsWith($sMobile, '0')) {
                $sMobile = '0' . $sMobile;
            }
            $studentData = [
                'guardian_id'   => $guardian->id,
                'name_en'       => strtoupper($row[1]),
                'name_bn'       => $row[2] ?? '',
                'mobile'        => $sMobile,
                'software_id'   => $software_id,
                'status'        => 'active',
                'password'      => $password,
                'created_from'  => 'excel',
            ];
            $data = $this->data + $studentData;

            $student = Student::create($data);

            $fMobile = $row[11] ?? '';
            if (!Str::startsWith($fMobile, '0')) {
                $fMobile = '0' . $fMobile;
            }
            $mMobile = $row[15] ?? '';
            if (!Str::startsWith($mMobile, '0')) {
                $mMobile = '0' . $mMobile;
            }

            // create profile
            $profileData = [
                'roll_number'       => $row[0] ?? '',
                'nid_or_birth_reg'  => $row[4],
                'dob' => !empty($row[5]) ? date('Y-m-d', strtotime($row[5])) : null,
                'gender'            => $row[6] ?? '',
                'religion'          => $row[7] ?? '',
                'disability'        => $row[8] ?? 'No',
                'fathers_name_en'   => strtoupper($row[9]),
                'fathers_name_bn'   => $row[10] ?? '',
                'fathers_mobile'    => $fMobile,
                'fathers_nid_or_birth_reg' => $row[12] ?? '',
                'mothers_name_en'   => strtoupper($row[13]),
                'mothers_name_bn'   => $row[14] ?? '',
                'mothers_mobile'    => $mMobile,
                'mothers_nid_or_birth_reg' => $row[16] ?? '',
            ];
            $student->profile()->create($profileData);

            // Send SMS
            if ($student) {
                // $this->sendSms($student->mobile, 'StudentCreate', [], $student);
            }
        }
    }
}
