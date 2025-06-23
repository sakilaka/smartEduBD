<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\Result\secondary;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Result\SecondaryGradeManagement;
use Exception;
use Illuminate\Http\Request;

class SecondaryGradeManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = SecondaryGradeManagement::latest('id');
        $query->whereLike($request->field_name, $request->value);

        if ($request->allData) {
            return SecondaryGradeManagement::oldest('from_mark')->get();
        }
        $datas  = $query->paginate($request->pagination);
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
                $res = SecondaryGradeManagement::create($request->all());
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SecondaryGradeManagement  $secondaryGradeManagement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SecondaryGradeManagement $secondaryGradeManagement)
    {
        return $secondaryGradeManagement;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SecondaryGradeManagement  $secondaryGradeManagement
     * @return \Illuminate\Http\Response
     */
    public function edit(SecondaryGradeManagement $secondaryGradeManagement)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SecondaryGradeManagement  $secondaryGradeManagement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SecondaryGradeManagement $secondaryGradeManagement)
    {
        if ($this->validateCheck($request, $secondaryGradeManagement->id)) {
            try {
                $secondaryGradeManagement->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SecondaryGradeManagement  $secondaryGradeManagement
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecondaryGradeManagement $secondaryGradeManagement)
    {
        if ($secondaryGradeManagement->delete()) {
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
        return true;
        return $request->validate([
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ]);
    }
}
