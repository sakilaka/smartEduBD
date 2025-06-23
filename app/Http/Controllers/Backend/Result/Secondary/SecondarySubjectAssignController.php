<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Result\Secondary;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\Institution;
use App\Models\Result\SecondarySubjectAssign;
use App\Models\Result\SecondarySubjectAssignDetails;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecondarySubjectAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = SecondarySubjectAssign::with('institution', 'medium', 'academic_class')->latest();
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('academic_class_id', $request->academic_class_id);

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

                $details = $request->details;
                $data    = $request->except('details');

                // exists check
                $exists = SecondarySubjectAssign::where([
                    'institution_id'    => $data['institution_id'],
                    'medium_id'         => $data['medium_id'],
                    'academic_class_id' => $data['academic_class_id'],
                ])->first();
                if (!empty($exists)) {
                    return response()->json(['error' => 'Sorry!! Already subject assign'], 200);
                }

                $subjectAssign = SecondarySubjectAssign::create($data);
                if ($subjectAssign) {
                    $subjectAssign->details()->createMany($details);
                }
                DB::commit();
                return response()->json(['message' => 'Create Successfully!', 'id' => $subjectAssign->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SecondarySubjectAssign  $secondarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SecondarySubjectAssign $secondarySubjectAssign)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $secondarySubjectAssign->load('details');
        return $secondarySubjectAssign;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubjectAssign  $secondarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(SecondarySubjectAssign $secondarySubjectAssign)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubjectAssign  $secondarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SecondarySubjectAssign $secondarySubjectAssign)
    {
        if ($this->validateCheck($request, $secondarySubjectAssign->id)) {
            try {
                DB::beginTransaction();
                $data = $request->except('details');

                $secondarySubjectAssign->update($data);

                // exists check
                $count = SecondarySubjectAssign::where([
                    'institution_id'    => $data['institution_id'],
                    'medium_id'         => $data['medium_id'],
                    'academic_class_id' => $data['academic_class_id'],
                ])->count();
                if ($count == 2) {
                    DB::rollBack();
                    return response()->json(['error' => 'Sorry!! Already subject assign'], 200);
                }

                // update, delete, insert
                GlobalHelper::updelsert(
                    'secondary_subject_assign_id',
                    $secondarySubjectAssign->id,
                    SecondarySubjectAssignDetails::class,
                    $request->details
                );

                DB::commit();
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubjectAssign  $secondarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecondarySubjectAssign $secondarySubjectAssign)
    {
        $secondarySubjectAssign->details()->delete();
        if ($secondarySubjectAssign->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subjectLists(Request $request)
    {
        $subjects = [];
        $subjectAssign = SecondarySubjectAssign::where([
            'institution_id'    => $request->institution_id,
            'medium_id'         => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
        ])->first();

        if (!empty($subjectAssign)) {
            $subjects = $subjectAssign->details()->with('subject')->get();
        }

        return response()->json($subjects);
    }

    /**
     * Validate form field.
     *
     * @return \Illuminate\Http\Response
     */
    public function validateCheck($request, $id = null)
    {
        return $request->validate([
            'details.*.subject_id' => 'required',
        ]);
    }
}
