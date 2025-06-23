<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Http\Controllers\Backend\SMS;

use App\Http\Resources\Resource;
use App\Models\SMS\SmsTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query  = SmsTransaction::latest('id');
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
                DB::beginTransaction();

                $data = $request->all();
                $invoice = SmsTransaction::store($data);

                // SET SSL Store ID & PASS
                $this->setStoreID(config('sslcommerz.wallet_store_id'), config('sslcommerz.wallet_store_pass'));

                // SSL Payment
                $pay      = app("App\Http\Controllers\BankApi\SslCommerzController");
                $response = $pay->index($invoice['id'], 'sms');

                if (empty($response)) {
                    return response()->json(['error' => "Sorry!! Payment cannot proceed at this time, please try again"], 201);
                }

                DB::commit();
                return response()->json(["payment_url" => $response, 'message' => "Invoice create successfully"], 200);
            } catch (Exception $ex) {
                DB::rollBack();
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SmsTransaction  $smsTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SmsTransaction $smsTransaction)
    {
        if ($request->format() == 'html') {
            return view('layouts.backend_app');
        }
        return $smsTransaction;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SmsTransaction  $smsTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(SmsTransaction $smsTransaction)
    {
        return view('layouts.backend_app');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SmsTransaction  $smsTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SmsTransaction $smsTransaction)
    {
        if ($this->validateCheck($request, $smsTransaction->id)) {
            try {
                $smsTransaction->update($request->all());
                return response()->json(['message' => 'Update Successfully!'], 200);
            } catch (Exception $ex) {
                return response()->json(['exception' => $ex->getMessage()], 422);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SmsTransaction  $smsTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(SmsTransaction $smsTransaction)
    {
        if ($smsTransaction->delete()) {
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
