<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\MasterSetup;

use App\Http\Controllers\Base\BaseController;
use App\Http\Resources\Resource;
use App\Models\MasterSetup\AccountHead;
use Illuminate\Http\Request;

class AccountHeadController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AccountHead::latest();
        $query->whereLike($request->field_name, $request->value);
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
        $res = AccountHead::create($request->all());
        return response()->json(['message' => 'Create Successfully!', 'id' => $res->id ?? ''], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountHead  $accountHead
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AccountHead $accountHead)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $accountHead;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AccountHead  $accountHead
     * @return \Illuminate\Http\Response
     */
    public function edit(AccountHead $accountHead)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AccountHead  $accountHead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountHead $accountHead)
    {
        $accountHead->update($request->all());
        return response()->json(['message' => 'Update Successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountHead  $accountHead
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountHead $accountHead)
    {
        if ($accountHead->update(['status' => 'deactive'])) {
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
            //ex: 'name' => 'required|email|nullable|date|string|min:0|max:191',
        ], [
            //ex: 'name' => "This name is required" (custom message)
        ]);
    }
}
