<?php

namespace App\Imports;

use App\Models\Admin;
use App\Traits\SMSTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\System\Role;

class TeachersImport implements ToCollection
{
    use SMSTrait;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $exists_arr = [];
        foreach ($rows as $key =>  $row) {
            if ($key == 0 || empty($row[3])) {
                continue;
            }

            $exists = Admin::where('email', $row[3])->exists();
            if ($exists) {
                array_push($exists_arr, $row);
                continue;
            }

            $password  = rand(111111, 999999);

            $data = [
                'type'              => $row[0],
                'name'              => $row[1],
                'mobile'            => $row[2],
                'email'             => $row[3],
                'status'            => 'active',
                'password'          => $password,
            ];

            // check role create or not
            $role = Role::where('type', $row[0])->first();
            if (!is_object($role)) {
                continue;
            }

            $data['role_id'] = $role->id;

            $teacher = Admin::create($data);

            $teacher->teacher()->create([]);

            // Send SMS
            if ($teacher) {
                $this->sendSms($teacher->mobile, 'TeacherCreate', ['password' => $password], $teacher);
            }
        }
    }
}
