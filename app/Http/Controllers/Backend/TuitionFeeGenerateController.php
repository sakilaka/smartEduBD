<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\Institution;
use App\Models\TuitionFeeGenerate;
use App\Models\TuitionFeeGenerateDetails;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class TuitionFeeGenerateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = TuitionFeeGenerate::with('institution', 'academic_class', 'medium', 'group', 'shift')->latest('id');
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('shift_id', $request->shift_id);
        $query->whereAny('institution_id', $institution_id);
        $datas = $query->paginate($request->pagination);
        return new Resource($datas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.backend_app');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function existSetup(Request $request)
    {
        $query = TuitionFeeGenerate::where([
            'institution_id' => $request->institution_id,
            'shift_id'      => $request->shift_id,
            'medium_id'     => $request->medium_id,
            'group_id'      => $request->group_id,
            'academic_class_id' => $request->academic_class_id,
        ]);
        if (!empty($request->id)) {
            $exist = $query->where('id', '!=', $request->id)->count();
        } else {
            $exist = $query->exists();
        }
        return response()->json($exist);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->validateCheck($request)) {
            try {
                DB::beginTransaction();

                // check create
                $exists = TuitionFeeGenerate::where([
                    'institution_id'    => $request->institution_id,
                    'shift_id'          => $request->shift_id,
                    'medium_id'         => $request->medium_id,
                    'group_id'          => $request->group_id,
                    'academic_class_id' => $request->academic_class_id,
                ])->exists();
                if ($exists) {
                    return response()->json(['error' => 'You have already generate fees for this hostel !'], 200);
                }

                $details = $request->details;
                $data    = $request->except('details');
                $res     = TuitionFeeGenerate::create($data);
                if ($res) {
                    $res->details()->createMany($details);
                }
                DB::commit();
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TuitionFeeGenerate  $tuitionFeeGenerate
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, TuitionFeeGenerate $tuitionFeeGenerate)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $tuitionFeeGenerate->load('details', 'institution', 'academic_class', 'medium', 'group');
        return $tuitionFeeGenerate;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TuitionFeeGenerate  $tuitionFeeGenerate
     * @return \Illuminate\Http\Response
     */
    public function edit(TuitionFeeGenerate $tuitionFeeGenerate)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TuitionFeeGenerate  $tuitionFeeGenerate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TuitionFeeGenerate $tuitionFeeGenerate)
    {
        try {
            $data = $request->except('details');

            $tuitionFeeGenerate->update($data);

            // update, delete, insert
            GlobalHelper::updelsert(
                'tuition_fee_generate_id',
                $tuitionFeeGenerate->id,
                TuitionFeeGenerateDetails::class,
                $request->details
            );

            DB::commit();
            return response()->json(['message' => 'Update Successfully!'], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(['exception' => $ex->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TuitionFeeGenerate  $tuitionFeeGenerate
     * @return \Illuminate\Http\Response
     */
    public function destroy(TuitionFeeGenerate $tuitionFeeGenerate)
    {
        $tuitionFeeGenerate->details()->delete();
        if ($tuitionFeeGenerate->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'institution_id'            => 'required',
            'account_head_id'           => 'required',
            'academic_class_id'         => 'required',
            'medium_id'                 => 'required',
            'group_id'                  => 'required',
            'details.*.amount'          => 'required_if:details.*.status,1,true',
        ],);
    }
}
