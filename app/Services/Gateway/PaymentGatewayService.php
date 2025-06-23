<?php

namespace App\Services\Gateway;

use App\Exceptions\PaymentException;
use App\Library\Gateway\ShurjoPayGateway;
use App\Library\Gateway\SslCommerzGateway;
use App\Services\Gateway\Traits\PaymentGatewayTrait;

class PaymentGatewayService
{
    use PaymentGatewayTrait;

    protected $config;
    protected $invoiceService;

    /**
     * Constructor method for PaymentGatewayService
     * 
     * @param InvoiceService $invoiceService 
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Initiates a payment process
     *
     * @param int $invoice_id 
     * @param \Illuminate\Support\Collection $gateway 
     * @return mixed 
     */
    public function initiate(int $invoice_id, $gateway)
    {
        // Retrieves the invoice data using the invoice ID and payment method
        $invoice = $this->invoiceService->getData($invoice_id, $gateway->provider);

        // Set Username and Password
        $this->setUsernameAndPassword($gateway);

        // Determines the correct payment gateway instance based on the method
        $gatewayInstance = $this->gatewayInstance($gateway->provider);

        // Initiates the payment process for the retrieved invoice
        return $gatewayInstance->makePayment($invoice);
    }

    /**
     * Returns the appropriate payment gateway instance based on the payment method.
     *
     * @param string $method
     * @return mixed
     * @throws PaymentException 
     */
    private function gatewayInstance(string $method)
    {
        switch ($method) {
            case 'SSL':
                return new SslCommerzGateway;

            case 'SPAY':
                return new ShurjoPayGateway;

            default:
                throw new PaymentException("Unsupported payment method: $method", 404);
        }
    }
}
