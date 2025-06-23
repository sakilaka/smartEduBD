<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\FeeSetup;
use App\Models\FeeSetupDetails;
use App\Models\MasterSetup\Institution;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class FeeSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $institution_id = Institution::current() ?? $request->institution_id;

        $query = FeeSetup::with('institution', 'academic_class', 'medium', 'group', 'shift')->latest('id');
        $query->whereAny('academic_class_id', $request->academic_class_id);
        $query->whereAny('medium_id', $request->medium_id);
        $query->whereAny('group_id', $request->group_id);
        $query->whereAny('institution_id', $institution_id);
        $query->whereAny('shift_id', $request->shift_id);
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
        $query = FeeSetup::where([
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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function feesLists(Request $request)
    {
        $fees  = FeeSetup::where([
            'institution_id' => $request->institution_id,
            'shift_id'      => $request->shift_id,
            'medium_id'     => $request->medium_id,
            'group_id'      => $request->group_id,
            'academic_class_id' => $request->academic_class_id,
        ])->first();

        if (!empty($fees)) {
            $details = $fees->details()
                ->select('ah.name_en', 'ah.id', 'fee_setup_details.payment_gateway_id', 'fee_setup_details.amount')
                ->join('account_heads as ah', 'ah.id', 'fee_setup_details.account_head_id')
                ->get();
        }

        return response()->json($details ?? []);
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

                $data     = $request->except('details');
                $details  = $request->details;
                $feeSetup = FeeSetup::create($data);
                if ($feeSetup) {
                    $feeSetup->details()->createMany($details);
                }
                DB::commit();
                return response()->json(['message' => 'Create Successfully!', 'id' => $feeSetup->id ?? ''], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FeeSetup  $feeSetup
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, FeeSetup $feeSetup)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        $feeSetup->load(['details.account_head', 'details.gateway', 'institution', 'academic_class', 'medium', 'group']);
        return $feeSetup;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FeeSetup  $feeSetup
     * @return \Illuminate\Http\Response
     */
    public function edit(FeeSetup $feeSetup)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeeSetup  $feeSetup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeeSetup $feeSetup)
    {
        if ($this->validateCheck($request, $feeSetup->id)) {
            try {
                DB::beginTransaction();
                $data = $request->except('details');

                $feeSetup->update($data);

                // update, delete, insert
                GlobalHelper::updelsert(
                    'fee_setup_id',
                    $feeSetup->id,
                    FeeSetupDetails::class,
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
     * @param  \App\Models\FeeSetup  $feeSetup
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeeSetup $feeSetup)
    {
        if ($feeSetup->delete()) {
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
            'details.*.account_head_id' => 'required',
            'details.*.payment_gateway_id' => 'required',
            'details.*.amount' => 'required',
        ]);
    }
}
