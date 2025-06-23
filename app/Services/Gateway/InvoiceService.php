<?php

namespace App\Services\Gateway;

use App\Exceptions\PaymentException;
use App\Models\Invoice;

class InvoiceService
{
    protected $data         = [];
    protected $name         = "";
    protected $email        = "";
    protected $phone        = "";
    protected $address      = "";
    protected $head_name    = "";
    protected $tranID       = "";
    protected $amount       = 0;
    protected $service_charge = 0;

    /**
     * Retrieves the invoice data based on the invoice ID and payment method.
     *
     * @param int $invoice_id
     * @param string $method
     * @return array 
     * @throws PaymentException If the invoice is not found.
     */
    public function getData(int $invoice_id, string $method = "SSL")
    {
        $trans_col  = 'invoice_number';
        $invoice    = $this->findInvoice(['id' => $invoice_id]);

        // If the invoice is found, sets various properties from the invoice data
        if (!empty($invoice) && is_object($invoice)) {
            $this->tranID       = $invoice[$trans_col] ?? null;
            $this->head_name    = $invoice->head->name_en ?? null;
            $this->amount       = $invoice->amount;
            $this->service_charge = $invoice->service_charge;
            $this->name         = $invoice->student->name_en ?? null;
            $this->phone        = $invoice->student->guardian->mobile ?? null;
            $this->email        = $invoice->student->guardian->email ?? null;
            $this->address      = $invoice->student->address ?? null;
        }

        // Sets the invoice data specific to the payment method
        $this->setInvoiceData($method);

        return $this->data;
    }

    /**
     * Finds and returns an invoice based on specific conditions.
     *
     * @param array $conditions An array of conditions to find the invoice (e.g., ['id' => $id]).
     * @return object|null
     */
    public function findInvoice(array $conditions)
    {
        $invoice = Invoice::where($conditions)->first();
        if (empty($invoice)) {
            throw new PaymentException("Invoice not found", 404);
        }
        return $invoice;
    }

    /**
     * Sets the invoice data according to the selected payment method.
     *
     * @param string $method
     * @return array
     */
    private function setInvoiceData(string $method)
    {
        $totalAmount = (float) $this->amount + (float) $this->service_charge;

        $this->data['amount']         = $totalAmount;
        $this->data['currency']       = "BDT";
        $this->data['tran_id']        = "$this->tranID";
        $this->data['customer_name']  = $this->name ?? "smartedu-user";
        $this->data['customer_email'] = $this->email ?? "smartedu@gmail.com";
        $this->data['customer_phone'] = $this->phone ?? "0111111111111";
        $this->data['customer_address'] = $this->address ?? "Dhaka";
        $this->data['customer_city']  = "Dhaka";

        // Adds additional fields specifically for the SSL payment method
        if ($method == 'SSL') {
            $this->data['customer_country'] = "Dhaka";
            $this->data['product_name']     = $this->head_name ?? "Fees";
        }

        return $this->data;
    }
}
