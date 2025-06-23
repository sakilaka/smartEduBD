<?php

namespace App\Library\Gateway;

use App\Exceptions\PaymentException;
use App\Services\Gateway\Traits\PaymentGatewayTrait;
use Illuminate\Support\Facades\Validator;

class ShurjoPayGateway extends AbstractPayment
{
    use PaymentGatewayTrait;

    private $data = [];
    private $config = [];
    private $spay_token;
    private $spay_store;
    private $success_url;
    private $cancel_url;
    private $ipn_url;
    private $header = [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer'
    ];

    /**
     * ShurjoPayGateway constructor.
     * Initializes the gateway configuration and sets the payment (prefix, username, and password).
     */
    public function __construct()
    {
        $this->config = config('shurjopay');

        $this->setPrefix($this->config['prefix']);
        $this->setUsername($this->config['credentials']['username']);
        $this->setPassword($this->config['credentials']['password']);
    }

    /**
     * Processes the payment using the provided request data.
     *
     * @param array $requestData The payment request data including customer and transaction details.
     * @return array|string 
     * @throws PaymentException 
     */
    public function makePayment(array $requestData)
    {
        // Validation rules for payment request data
        $rules = [
            'tran_id'           => 'required|string',
            'amount'            => 'required|numeric|min:0',
            'currency'          => 'required|string|max:3',
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:15',
            'customer_email'    => 'required|email|max:255',
            'customer_address'  => 'required|string|max:255',
            'customer_city'     => 'required|string|max:255',
        ];

        // Validate the request data
        $validator = Validator::make($requestData, $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new PaymentException($errors->first(), 422, new \Exception, $errors);
        }

        // Generate the authentication token
        $this->generateToken();

        // Set the API URL for making the payment
        $this->setApiUrl($this->config['api_url']['payment']);

        // Set the necessary parameters for the payment
        $this->setParams($requestData);

        // Include authentication information in the request data
        $this->setAuthenticationInfo();

        // Call the payment API and retrieve the response
        $response = $this->callToApi($this->data, $this->header);

        // Return the checkout URL if successful
        if (!empty($response['checkout_url'])) {
            return [
                'status'            => 'success',
                'invoice_number'    => $response['customer_order_id'],
                'order_id'          => $response['sp_order_id'],
                'customer_name'     => $response['customer_name'],
                'customer_phone'    => $response['customer_phone'],
                'checkout_url'      => $response['checkout_url']
            ];
        }

        // Throw an exception if payment cannot proceed
        throw new PaymentException("Sorry!! Payment cannot proceed at this time, please try again", 400);
    }

    /**
     * Generates an authentication token for ShurjoPay.
     * 
     * @return bool
     */
    private function generateToken()
    {
        // Generate token if not already set
        if (!$this->spay_token) {
            $this->spay_token = $this->authenticate();
        }
        return true;
    }

    /**
     * Authenticates the user and retrieves the token and store ID.
     * 
     * @return string|null
     * @throws PaymentException 
     */
    protected function authenticate()
    {
        // Set the API URL for token authentication
        $this->setApiUrl($this->config['api_url']['token']);

        // Ensure username and password are provided
        if (empty($this->getUsername()) || empty($this->getPassword())) {
            throw new PaymentException("Authentication process cannot continue as username or password is empty", 422);
        }

        // Call the API to authenticate and retrieve token and store ID
        $response = $this->callToApi($this->config['credentials']);
        if (is_array($response)) {
            $this->spay_token = $response['token'] ?? '';
            $this->spay_store = $response['store_id'] ?? '';
            $this->header['Authorization'] = "Bearer {$this->spay_token}";
            return $this->spay_token;
        }
        return null;
    }

    /**
     * Validates a ShurjoPay order using the provided order ID.
     *
     * @param string $order_id 
     * @return array 
     */
    public function paymentValidate($order_id, $gateway = [])
    {
        $configs = $this->setUsernameAndPassword($gateway);
        $this->setPrefix($this->config['prefix']);
        $this->setUsername($this->config['credentials']['username']);
        $this->setPassword($this->config['credentials']['password']);

        // Generate authentication token
        $this->generateToken();

        // Set the API URL for validate order
        $this->setApiUrl($this->config['api_url']['validate']);

        // Call the validation API
        $data = ['order_id' => $order_id];
        $response = $this->callToApi($data, $this->header);

        // Return the response 
        if (!empty($response) && count((array) $response) > 0) {
            $responseData = $response[0] ?? [];

            if (!empty($responseData['order_id']) && $responseData['order_id'] == $order_id) {
                return $responseData;
            }
        }

        return [];
    }

    /**
     * Sets various parameters for the payment process.
     * 
     * @param array $requestData
     * @return void
     */
    public function setParams($requestData)
    {
        ## Set required payment parameters
        $this->setRequiredInfo($requestData);

        ## Set customer information
        $this->setCustomerInfo($requestData);

        ##  Shipment Information
        $this->setShipmentInfo($requestData);

        ##  Customized or Additional Parameters
        $this->setAdditionalInfo($requestData);
    }

    /**
     * Sets authentication info, such as token and store ID.
     * 
     * @return array
     */
    public function setAuthenticationInfo()
    {
        $this->data['prefix']   = $this->config['prefix'];
        $this->data['token']    = $this->spay_token;
        $this->data['store_id'] = $this->spay_store;

        return $this->data;
    }

    /**
     * Sets required transaction info
     * 
     * @param array $info 
     * @return array
     */
    public function setRequiredInfo(array $info)
    {
        $this->data['order_id'] = $info['tran_id'];
        $this->data['amount'] = $info['amount'];
        $this->data['currency'] = $info['currency'];

        $this->data['discount_amount'] = $info['discount_amount'] ?? 0;
        $this->data['disc_percent'] = $info['disc_percent'] ?? 0;

        // Set success and cancel URLs
        $this->setSuccessUrl();
        $this->setCancelUrl();
        $this->setIPNUrl();

        $this->data['return_url'] = $this->getSuccessUrl();
        $this->data['cancel_url'] = $this->getCancelUrl();
        $this->data['ipn_url'] = $this->getIPNUrl();

        return $this->data;
    }

    /**
     * Sets customer information
     * 
     * @param array $info
     * @return array
     */
    public function setCustomerInfo(array $info)
    {
        $this->data['customer_name'] = $info['customer_name'];
        $this->data['customer_address'] = $info['customer_address'];
        $this->data['customer_email'] = $info['customer_email'];
        $this->data['customer_phone'] = $info['customer_phone'];
        $this->data['customer_city'] = $info['customer_city'];
        $this->data['customer_post_code'] = $info['customer_post_code'] ?? '';
        $this->data['customer_state'] = $info['customer_state'] ?? '';
        $this->data['customer_country'] = $info['customer_country'] ?? '';
        $this->data['client_ip'] = $info['client_ip'] ?? '';

        return $this->data;
    }

    /**
     * Sets shipment information for the payment
     * 
     * @param array $info
     * @return array 
     */
    public function setShipmentInfo(array $info)
    {
        $this->data['shipping_address'] = $info['shipping_address'] ?? '';
        $this->data['shipping_city'] = $info['shipping_city'] ?? '';
        $this->data['received_person_name'] = $info['received_person_name'] ?? '';
        $this->data['shipping_phone_number'] = $info['shipping_phone_number'] ?? '';

        return $this->data;
    }

    /**
     * Sets product information for the payment
     * 
     * @param array $info 
     * @return void
     */
    public function setProductInfo(array $info)
    {
    }

    /**
     * Sets additional parameters for the payment request.
     * This can include optional fields like client IP and custom values.
     * 
     * @param array $info
     * @return array 
     */
    public function setAdditionalInfo(array $info)
    {
        $this->data['client_ip'] = $info['client_ip'] ?? '';
        $this->data['value1'] = $info['value1'] ?? '';
        $this->data['value2'] = $info['value2'] ?? '';
        $this->data['value3'] = $info['value3'] ?? '';
        $this->data['value4'] = $info['value4'] ?? '';

        return $this->data;
    }

    /**
     * Sets the success URL for redirecting after a successful payment.
     * 
     * @return void
     */
    protected function setSuccessUrl()
    {
        $this->success_url = rtrim(env('APP_URL'), '/') . $this->config['success_url'];
    }

    /**
     * Gets the success URL for redirecting after a successful payment.
     * 
     * @return string T
     */
    protected function getSuccessUrl()
    {
        return $this->success_url;
    }

    /**
     * Sets the cancel URL for redirecting after a cancelled payment.
     * 
     * @return void
     */
    protected function setCancelUrl()
    {
        $this->cancel_url = rtrim(env('APP_URL'), '/') . $this->config['cancel_url'];
    }

    /**
     * Gets the cancel URL for redirecting after a cancelled payment.
     * 
     * @return string
     */
    protected function getCancelUrl()
    {
        return $this->cancel_url;
    }

    /**
     * Sets the ipn URL for redirecting after a ipn payment.
     * 
     * @return void
     */
    protected function setIPNUrl()
    {
        $this->ipn_url = rtrim(env('APP_URL'), '/') . $this->config['ipn_url'];
    }

    /**
     * Gets the ipn URL for redirecting after a ipn payment.
     * 
     * @return string
     */
    protected function getIPNUrl()
    {
        return $this->ipn_url;
    }
}
