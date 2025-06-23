<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Exam;
use App\Models\MasterSetup\Institution;
use App\Models\MobileAppVersion;
use App\Traits\Lib\CustomDataTrait;
use App\Traits\Lib\DynamicDataTrait;
use Illuminate\Support\Facades\App;

class LibController extends Controller
{
    use DynamicDataTrait, CustomDataTrait;
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $globalData = [];
        $globalData += $this->commonCustomData();
        $globalData += $this->dynamicData();
        $globalData += $this->commonDynamicData();

        $data = [
            'config' => App::make('siteSettingObj'),
            'global' => $globalData,
        ];

        return $this->sendResponse($data);
    }

    /**
     * Mobile app version
     *
     * @return \Illuminate\Http\Response
     */
    public function mobileAppVersion()
    {
        $data = MobileAppVersion::select('android', 'ios')->first();
        return response()->json($data);
    }

    /**
     * Dynamic data
     */
    private function dynamicData()
    {
        $insID      = auth()->guard('guardianApi')->user()->current_student->institution_id ?? '';
        $exams      = Exam::select('id', 'name')->get()->toArray();
        $campuses   = Campus::select('id', 'institution_id', 'name')->where('institution_id', $insID)->get()->toArray();
        $institutions = Institution::select('id', 'name')->with('class_mappings', 'campuses')
            ->when($insID, function ($q, $insID) {
                $q->where('id', $insID);
            })
            ->get()
            ->toArray();

        return compact(
            'campuses',
            'exams',
            'institutions',
        );
    }
}
