<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Resources\Resource;
use App\Models\MasterSetup\AcademicClass;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AcademicClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = AcademicClass::oldest('sorting')->with('institution_category');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('institution_category_id', $request->institution_category_id);

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
                $res = AcademicClass::create($request->all());
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicClass  $academicClass
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AcademicClass $academicClass)
    {
        return $academicClass;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicClass  $academicClass
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicClass $academicClass)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicClass  $academicClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicClass $academicClass)
    {
        if ($this->validateCheck($request, $academicClass->id)) {
            try {
                $academicClass->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicClass  $academicClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicClass $academicClass)
    {
        if ($academicClass->delete()) {
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
