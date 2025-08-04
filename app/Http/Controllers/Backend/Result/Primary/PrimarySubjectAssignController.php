<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Result\Primary;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\Institution;
use App\Models\Result\PrimarySubjectAssign;
use App\Models\Result\PrimarySubjectAssignDetails;
use App\Models\TeacherSubjectAssign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimarySubjectAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = PrimarySubjectAssign::with('institution', 'medium', 'academic_class')->latest();
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
                $exists = PrimarySubjectAssign::where([
                    'institution_id'    => $data['institution_id'],
                    'medium_id'         => $data['medium_id'],
                    'academic_class_id' => $data['academic_class_id'],
                ])->first();
                if (!empty($exists)) {
                    return response()->json(['error' => 'Sorry!! Already subject assign'], 200);
                }

                $subjectAssign = PrimarySubjectAssign::create($data);
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
     * @param  \App\Models\PrimarySubjectAssign  $primarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, PrimarySubjectAssign $primarySubjectAssign)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $primarySubjectAssign->load('details');
        return $primarySubjectAssign;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubjectAssign  $primarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(PrimarySubjectAssign $primarySubjectAssign)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubjectAssign  $primarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrimarySubjectAssign $primarySubjectAssign)
    {
        if ($this->validateCheck($request, $primarySubjectAssign->id)) {
            try {
                DB::beginTransaction();
                $data = $request->except('details');

                $primarySubjectAssign->update($data);

                // exists check
                $count = PrimarySubjectAssign::where([
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
                    'primary_subject_assign_id',
                    $primarySubjectAssign->id,
                    PrimarySubjectAssignDetails::class,
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
     * @param  \App\Models\SubjectAssign  $primarySubjectAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrimarySubjectAssign $primarySubjectAssign)
    {
        $primarySubjectAssign->details()->delete();
        if ($primarySubjectAssign->delete()) {
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

        // Step 1: Check for subject assignment based on institution/class/medium
        $subjectAssign = PrimarySubjectAssign::where([
            'institution_id'    => $request->institution_id,
            'medium_id'         => $request->medium_id,
            'academic_class_id' => $request->academic_class_id,
        ])->first();

        // Step 2: If assignment exists
        if (!empty($subjectAssign)) {
            // Base subjects from assignment
            $assignedSubjectDetails = $subjectAssign->details()->with('subject')->get();

            // Step 3: Check if the logged-in user is a teacher
            $authAdminId = auth()->id();
            $teacherSubjectIds = TeacherSubjectAssign::where('admin_id', $authAdminId)
                ->pluck('subject_id')
                ->toArray();

            // Step 4: Filter based on teacher subject_ids, if any found
            if (!empty($teacherSubjectIds)) {
                $assignedSubjectDetails = $assignedSubjectDetails->filter(function ($item) use ($teacherSubjectIds) {
                    return in_array($item->subject_id, $teacherSubjectIds);
                })->values(); // reset keys
            }

            // Set subjects
            $subjects = $assignedSubjectDetails;
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
