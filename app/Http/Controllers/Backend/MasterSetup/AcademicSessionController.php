<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\AcademicSession;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AcademicSession::latest('name')->with('institution_category');
        $query->whereLike($request->field_name, $request->value);
        $query->whereAny('institution_category_id', $request->institution_category_id);
        $query->whereAny('status', $request->status);

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
                if ($request->current) {
                    AcademicSession::where('current', 1)->where('institution_category_id', $request->institution_category_id)->update(['current' => 0]);
                }

                $res = AcademicSession::create($request->all());
                Artisan::call('cache:clear');
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AcademicSession $academicSession)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $academicSession;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicSession $academicSession)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicSession $academicSession)
    {
        if ($this->validateCheck($request, $academicSession->id)) {
            try {
                if ($request->current) {
                    AcademicSession::where('current', 1)->where('institution_category_id', $request->institution_category_id)->update(['current' => 0]);
                }
                $data = $request->all();

                $academicSession->update($data);

                if ($request->current) {
                    AcademicSession::where('id', $academicSession->id)->where('institution_category_id', $request->institution_category_id)->update(['current' => 1]);
                }

                Artisan::call('cache:clear');
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicSession  $academicSession
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicSession $academicSession)
    {
        if ($academicSession->update(['status' => 'deactive'])) {
            Artisan::call('cache:clear');
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
