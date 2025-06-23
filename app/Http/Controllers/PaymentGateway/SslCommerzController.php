<?php

namespace App\Http\Controllers\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Library\Gateway\SslCommerzGateway;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Services\Gateway\InvoiceService;
use Illuminate\Support\Facades\Log;

class SslCommerzController extends Controller
{
    /**
     * Handles payment success callback from SslCommerz.
     * 
     * @param SslCommerzGateway $sslCommerz
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed 
     */
    public function success(SslCommerzGateway $sslCommerz, InvoiceService $invoiceService, Request $request)
    {
        $tran_id  = $request->input('tran_id');

        if (!empty($tran_id)) {
            if ($request->status == 'VALID') {

                $invoice = $invoiceService->findInvoice(['invoice_number' => $tran_id]);
                $validateData = $sslCommerz->paymentValidate($tran_id, $invoice->gateway, $request->all());
                if (!empty($validateData)) {

                    Invoice::paymentSuccess($validateData);
                    return $this->redirectURL('success', $invoice);
                }
            }

            return $this->failed(new InvoiceService, new Request(['tran_id' => $tran_id]));
        }
        return $this->redirectURL('failed');
    }

    /**
     * Handles payment failed callback from SslCommerz.
     * 
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed 
     */
    public function failed(InvoiceService $invoiceService, Request $request)
    {
        $tran_id = $request->input('tran_id');

        $invoice = $invoiceService->findInvoice(['invoice_number' => $tran_id]);
        if (!empty($invoice)) {
            if ($invoice->status == 'pending') {
                $invoice->update(['status' => 'failed']);

                // failed invoice sms notify
                // $this->sendSms('01303009052', 'FailedPayment', ['invoice_id' => $invoice['invoice_number']]);
            }
        }

        return $this->redirectURL('failed', $invoice);
    }

    /**
     * Handles payment cancellation callback from SslCommerz.
     * 
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed 
     */
    public function cancel(InvoiceService $invoiceService, Request $request)
    {
        $tran_id = $request->input('tran_id');

        $invoice = $invoiceService->findInvoice(['invoice_number' => $tran_id]);
        if (!empty($invoice)) {
            if ($invoice->status == 'pending') {
                $invoice->update(['status' => 'cancelled']);
                return $this->redirectURL('cancel', $invoice);
            }
        }

        return $this->redirectURL('cancel', $invoice);
    }

    /**
     * Handles payment ipn callback from SslCommerz.
     * 
     * @param SslCommerzGateway $sslCommerz
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed 
     */
    public function ipn(SslCommerzGateway $sslCommerz, InvoiceService $invoiceService, Request $request)
    {
        $tran_id = $request->input('tran_id');
        // Log::channel('daily')->debug("SslCommerzGateway IPN Response : $tran_id");

        if (!empty($tran_id)) {
            $invoice = $invoiceService->findInvoice(['invoice_number' => $tran_id]);

            if (!empty($invoice)) {
                if ($invoice->status == 'pending') {

                    $validateData = $sslCommerz->paymentValidate($tran_id, $invoice->gateway, $request->all());
                    // Log::channel('daily')->debug("Validate Data", $validateData);
                    if (!empty($validateData)) {
                        $requestData = $request->all();
                        Invoice::paymentSuccess($requestData);

                        // Log::channel('daily')->debug("SslCommerzGateway IPN Success From Software");
                        return response()->json("Transaction is successfully Completed");
                    } else {
                        // Log::channel('daily')->debug("SslCommerzGateway IPN Response :(", $request->all());
                        $invoice->update(['status' => 'failed']);
                        return response()->json("validation Fail");
                    }
                }
            }
        }

        // Log::channel('daily')->debug("IPN Response :(", $request->all());
        return response()->json("Invalid Transaction");
    }

    /**
     * Handles redirection based on payment result.
     * 
     * @param string $status - The payment status ('success', 'failed', or 'cancel').
     * @param Invoice|null $invoice 
     * @return void 
     */
    public function redirectURL($status, $invoice = null)
    {
        $redirect_url = config('sslcommerz.redirect_url') ?? url('/');
        $created_from = $invoice->created_from ?? '';

        $redirect = $created_from == 'admin' ? url('admin/studentPayment') : "{$redirect_url}/payment-history";
        $url =  "{$redirect_url}/ssl-response?redirect={$redirect}&status={$status}&created_from={$created_from}";
        header('Location: ' . $url, true, 302);
        exit();
    }
}
