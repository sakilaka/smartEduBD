<?php

namespace App\Http\Controllers\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Library\Gateway\ShurjoPayGateway;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Gateway\InvoiceService;
use App\Services\Gateway\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShurjoPayController extends Controller
{
    /**
     * Handles the payment success/failed callback from ShurjoPay.
     * 
     * @param ShurjoPayGateway $shurjoPay 
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed
     */
    public function success(ShurjoPayGateway $shurjoPay, InvoiceService $invoiceService, Request $request)
    {
        $order_id = $request->order_id;
        if (!empty($order_id)) {

            $invoice = $invoiceService->findInvoice(['gateway_order_id' => $order_id]);
            if (!empty($invoice)) {

                $validateData = $shurjoPay->paymentValidate($order_id, $invoice->gateway);
                if (!empty($validateData)) {

                    if ($validateData['bank_status'] == "Success" || $validateData['bank_status'] == "Completed") {

                        $requestData = new Request([
                            'tran_id' => $validateData['customer_order_id'],
                            'bank_tran_id' => $validateData['bank_trx_id'],
                            'card_type' => $validateData['method'],
                            'payment_method' => "SPAY",
                        ]);

                        Invoice::paymentSuccess($requestData);
                        return $this->redirectURL('success', $invoice);
                    }

                    return $this->failed(new InvoiceService, $validateData['customer_order_id']);
                }
            }
        }

        return $this->redirectURL('failed');
    }

    /**
     * Handles payment failed
     * 
     * @param InvoiceService $invoiceService
     * @param string $invoice_id 
     * @return mixed 
     */
    private function failed(InvoiceService $invoiceService, $invoice_id)
    {
        $invoice = $invoiceService->findInvoice(['invoice_number' => $invoice_id]);
        $invoice->update(['status' => 'failed', 'payment_method' => 'SPAY']);

        return $this->redirectURL('failed', $invoice);
    }

    /**
     * Handles payment cancellation callback from ShurjoPay.
     * 
     * @param ShurjoPayGateway $shurjoPay 
     * @param InvoiceService $invoiceService
     * @param Request $request 
     * @return mixed 
     */
    public function cancel(ShurjoPayGateway $shurjoPay, InvoiceService $invoiceService, Request $request)
    {
        $order_id = $request->order_id;

        $invoice = $invoiceService->findInvoice(['gateway_order_id' =>  $order_id]);
        if (!empty($invoice)) {
            $validateData = $shurjoPay->paymentValidate($order_id, $invoice->gateway);
            if (!empty($validateData)) {
                $invoice = $invoiceService->findInvoice(['invoice_number' => $validateData['customer_order_id']]);
                $invoice->update(['status' => 'cancelled', 'payment_method' => 'SPAY']);
            }
        }

        return $this->redirectURL('cancel', $invoice);
    }

    /**
     * Handles payment ipn callback from ShurjoPayGateway.
     * 
     * @param ShurjoPayGateway $shurjoPay
     * @param InvoiceService $invoiceService 
     * @param Request $request 
     * @return mixed 
     */
    public function ipn(ShurjoPayGateway $shurjoPay, InvoiceService $invoiceService, Request $request)
    {
        $order_id = $request->order_id;
        // Log::channel('daily')->debug("ShurjoPay IPN Response : $order_id");

        $invoice = $invoiceService->findInvoice(['gateway_order_id' => $order_id]);
        if (!empty($invoice)) {

            $validateData = $shurjoPay->paymentValidate($order_id, $invoice->gateway);
            if (!empty($validateData)) {

                if ($validateData['bank_status'] == "Success" || $validateData['bank_status'] == "Completed") {

                    $requestData = new Request([
                        'tran_id' => $validateData['customer_order_id'],
                        'bank_tran_id' => $validateData['bank_trx_id'],
                        'card_type' => $validateData['method'],
                        'payment_method' => "SPAY",
                    ]);

                    Invoice::paymentSuccess($requestData);
                    Log::channel('daily')->debug("ShurjoPay IPN Success From Software");
                    return response()->json("Transaction is successfully Completed");
                } else {
                    return response()->json("validation Fail");
                }
            } else {
                Log::channel('daily')->debug("validation Fail");
                return response()->json("validation Fail");
            }
        }

        // Log::channel('daily')->debug("ShurjoPay IPN Response :(", $request->all());
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
        $redirect_url = config('shurjopay.redirect_url') ?? url('/');
        $created_from = $invoice->created_from ?? '';

        $redirect = $created_from == 'admin' ? url('admin/studentPayment') : "{$redirect_url}/payment-history";
        $url =  "{$redirect_url}/ssl-response?redirect={$redirect}&status={$status}&created_from={$created_from}";
        header('Location: ' . $url, true, 302);
        exit();
    }
}
