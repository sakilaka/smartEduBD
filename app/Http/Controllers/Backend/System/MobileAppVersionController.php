<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\MobileAppVersion;
use Exception;
use Illuminate\Http\Request;

class MobileAppVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = MobileAppVersion::latest('id');
        $query->whereLike($request->field_name, $request->value);

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
                $res = MobileAppVersion::create($request->all());
                return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MobileAppVersion  $mobileAppVersion
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, MobileAppVersion $mobileAppVersion)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $mobileAppVersion;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MobileAppVersion  $mobileAppVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MobileAppVersion $mobileAppVersion)
    {
        if ($this->validateCheck($request, $mobileAppVersion->id)) {
            try {
                $mobileAppVersion->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
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
