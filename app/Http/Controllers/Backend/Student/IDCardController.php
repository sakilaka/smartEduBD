<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Institution;
use App\Models\Student;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class IDCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $take     = $request->take ?? 100;
        $skip     = $request->skip;
        $order_by = $request->order_by;

        $query = Student::select(
            'students.id',
            'students.name_en',
            'software_id',
            'pro.roll_number',
            'pro.dob',
            'pro.nid_or_birth_reg',
            'pro.fathers_name_en',
            'pro.mothers_name_en',
            'pro.present_address',
            'ac.name as academic_class_name',
            'ga.name_en as guardian_name',
            'ga.mobile as guardian_mobile',
        )
            ->join('guardians as ga', 'ga.id', 'students.guardian_id')
            ->join('student_profiles as pro', 'pro.student_id', 'students.id')
            ->join('academic_classes as ac', 'ac.id', 'students.academic_class_id')
            ->with('profile:profile,student_id')
            ->orderBy('students.id', $order_by);

        if (in_array($request->field_name, ['roll_number'])) {
            $query->whereSubLike('profile', $request->field_name, $request->value);
        } else {
            $query->whereLike($request->field_name, $request->value);
        }

        $query->where('profile', '!=', '');
        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('campus_id', $request->campus_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('section_id', $request->section_id);

        if ($request->custom_rolls) {
            $rolls = str_replace(' ', '', $request->roll_lists);
            $rolls = explode(',', $rolls);
            $query->whereIn('software_id', $rolls);
        }

        $data['students'] = $query->skip($skip)->take($take)->get()->map(function ($item) {
            $item['qr_code'] = $this->QrCodeData($item);
            return $item;
        });

        $data['institution'] = Institution::find($institution_id);

        return $data;
    }

    /**
     * QR Code Generate
     *
     * @return \Illuminate\Http\Response
     */
    public function QrCodeData($item)
    {
        $address = preg_replace('/[[:^print:]]/', '', $item->present_address);

        $info = [
            "ID: $item->software_id",
            "Address: $address",
        ];
        $data = implode("\n", $info);
        $qr = base64_encode(QrCode::format('png')->size(300)->generate($data));
        return $qr;
    }
}
