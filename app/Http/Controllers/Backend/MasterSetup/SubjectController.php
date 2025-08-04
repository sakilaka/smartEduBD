<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\Subject;
use App\Models\Result\PrimarySubjectAssign;
use App\Models\Result\PrimarySubjectAssignDetails;
use App\Models\Result\SecondarySubjectAssign;
use App\Models\Result\SecondarySubjectAssignDetails;
use Exception;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Subject::oldest('sorting')->with('institution_category');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('institution_category_id', $request->institution_category_id);

        if ($request->allData) {
            return $query->select('id', 'name_en as name')->get();
        } else {
            $datas = $query->paginate($request->pagination);
            return new Resource($datas);
        }
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
                $res = Subject::create($request->all());
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Subject $subject)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $subject;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        return view('layouts.backend_app');
    }

    public function update(Request $request, Subject $subject)
    {
        if ($this->validateCheck($request, $subject->id)) {
            try {
                $subject->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    public function destroy(Subject $subject)
    {
        if ($subject->delete()) {
            return response()->json(['message' => 'Delete Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Delete Unsuccessfully!'], 200);
        }
    }

    public function validateCheck($request, $id = null)
    {
        return true;
        return $request->validate([
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ]);
    }

    public function getSubjectsByClass($classId, $groupId)
    {
        $assign = PrimarySubjectAssign::where('academic_class_id', $classId)->first();

        if ($assign) {
            $subjectIds = PrimarySubjectAssignDetails::where('primary_subject_assign_id', $assign->id)
                ->pluck('subject_id');
        } else {
            // If not found in primary, try secondary
            $assign = SecondarySubjectAssign::where('academic_class_id', $classId)->where('academic_group_id', $groupId)->first();

            if ($assign) {
                $subjectIds = SecondarySubjectAssignDetails::where('secondary_subject_assign_id', $assign->id)
                    ->pluck('subject_id');
            } else {
                return response()->json([]);
            }
        }

        $subjects = Subject::whereIn('id', $subjectIds)->get(['id', 'name_en']);

        return response()->json($subjects);
    }
}
