<?php

namespace App\Library\Gateway;

use App\Exceptions\PaymentException;
use App\Services\Gateway\Traits\PaymentGatewayTrait;
use Illuminate\Support\Facades\Validator;

class SslCommerzGateway extends AbstractPayment
{
    use PaymentGatewayTrait;

    private $data = [];
    private $config = [];
    private $success_url;
    private $cancel_url;
    private $failed_url;
    private $ipn_url;

    /**
     * SSL Payment constructor.
     * Initializes the gateway configuration and sets the payment (store ID and store password).
     */
    public function __construct()
    {
        $this->config = config('sslcommerz');

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
            'product_name'      => 'required|string|max:255',
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:15',
            'customer_email'    => 'required|email|max:255',
            'customer_address'  => 'required|string|max:255',
            'customer_city'     => 'required|string|max:255',
            'customer_country'  => 'required|string|max:255',
        ];

        // Validate the request data
        $validator = Validator::make($requestData, $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            throw new PaymentException($errors->first(), 422, new \Exception, $errors);
        }

        // Set the API URL for making the payment
        $this->setApiUrl($this->config['api_url']['payment']);

        // Set the necessary parameters for the payment
        $this->setParams($requestData);

        // Include authentication information in the request data
        $this->setAuthenticationInfo();

        $header = [];
        $sendAsForm = true;
        // Call the payment API and retrieve the response
        $response = $this->callToApi($this->data, $header, $sendAsForm);

        // Return the checkout URL if successful
        if (!empty($response['redirectGatewayURL'])) {
            return [
                'status' => 'success',
                'store_name' => $response['store_name'] ?? '',
                'store_logo' => $response['storeLogo'] ?? '',
                'checkout_url' => $response['GatewayPageURL']
            ];
        } else if ($response['failedreason']) {
            throw new PaymentException($response['failedreason'], 400);
        }
        throw new PaymentException("Sorry!! Payment cannot proceed at this time, please try again", 400);
    }


    /**
     * Validates a SSLCommerz order using the provided tran ID.
     *
     * @param string $tran_id 
     * @return array 
     */
    public function paymentValidate($tran_id, $gateway = [], $post_data = [])
    {
        $this->setUsernameAndPassword($gateway);
        $this->setUsername($this->config['credentials']['username']);
        $this->setPassword($this->config['credentials']['password']);

        $val_id = $post_data['val_id'] ?? '';
        $store_id = $this->getUsername();
        $store_passwd = $this->getPassword();

        $params = "?val_id={$val_id}&store_id={$store_id}&store_passwd={$store_passwd}&v=1&format=json";
        $requested_url = $this->config['api_url']['validate'] . $params;

        // Set the API URL for validate order
        $this->setApiUrl($requested_url);

        // Call the payment API and retrieve the response
        $response = $this->callToApi($this->data);

        // Return the response
        if (!empty($response) && !empty($response['tran_id']) && $response['tran_id'] == $tran_id) {
            if ($response['status'] == "VALID" || $response['status'] == "VALIDATED") {
                return $response;
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
        ##  Integration Required Parameters
        $this->setRequiredInfo($requestData);

        ##  Customer Information
        $this->setCustomerInfo($requestData);

        ##  Shipment Information
        $this->setShipmentInfo($requestData);

        ##  Product Information
        $this->setProductInfo($requestData);

        ##  Customized or Additional Parameters
        $this->setAdditionalInfo($requestData);
    }

    /**
     * Sets authentication info, such as store id and store password.
     * 
     * @return array
     */
    public function setAuthenticationInfo()
    {
        $this->data['store_id'] = $this->getUsername();
        $this->data['store_passwd'] = $this->getPassword();

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
        $this->data['total_amount'] = $info['amount'];
        $this->data['currency'] = $info['currency'];
        $this->data['tran_id'] = $info['tran_id'];
        $this->data['product_category'] =  $info['product_category'] ?? 'College/School Fees';


        // Set success,failed,ipn and cancel URLs
        $this->setSuccessUrl();
        $this->setFailedUrl();
        $this->setCancelUrl();
        $this->setIPNUrl();

        $this->data['success_url'] = $this->getSuccessUrl();
        $this->data['fail_url'] = $this->getFailedUrl();
        $this->data['cancel_url'] = $this->getCancelUrl();
        $this->data['ipn_url'] = $this->getIPNUrl();

        $this->data['multi_card_name'] = $info['multi_card_name'] ?? '';
        $this->data['allowed_bin'] = $info['allowed_bin'] ?? '';

        ##   Parameters to Handle EMI Transaction ##
        $this->data['emi_option'] = $info['emi_option'] ?? '';
        $this->data['emi_max_inst_option'] = $info['emi_max_inst_option'] ?? '';
        $this->data['emi_selected_inst'] = $info['emi_selected_inst'] ?? '';
        $this->data['emi_allow_only'] = $info['emi_allow_only'] ?? 0;

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
        $this->data['cus_name'] = $info['customer_name'] ?? '';
        $this->data['cus_email'] = $info['customer_email'] ?? '';
        $this->data['cus_add1'] = $info['customer_address'] ?? '';
        $this->data['cus_add2'] = $info['customer_add2'] ?? '';
        $this->data['cus_city'] = $info['customer_city'] ?? '';
        $this->data['cus_state'] = $info['customer_state'] ?? '';
        $this->data['cus_postcode'] = $info['customer_postcode'] ?? '';
        $this->data['cus_country'] = $info['customer_country'] ?? '';
        $this->data['cus_phone'] = $info['customer_phone'] ?? '';
        $this->data['cus_fax'] = $info['customer_fax'] ?? '';
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
        $this->data['shipping_method'] = $info['shipping_method'] ?? 'NO';
        $this->data['num_of_item'] = $info['num_of_item'] ?? 1;
        $this->data['ship_name'] = $info['ship_name'] ?? '';
        $this->data['ship_add1'] = $info['ship_add1'] ?? '';
        $this->data['ship_add2'] = $info['ship_add2'] ?? '';
        $this->data['ship_city'] = $info['ship_city'] ?? '';
        $this->data['ship_state'] = $info['ship_state'] ?? '';
        $this->data['ship_postcode'] = $info['ship_postcode'] ?? '';
        $this->data['ship_country'] = $info['ship_country'] ?? '';
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
        $this->data['product_category'] = $info['product_category'] ?? 'Goods';
        $this->data['product_name'] = $info['product_name'];
        $this->data['product_profile'] = $info['product_profile'] ?? 'physical-goods';

        $this->data['hours_till_departure'] = $info['hours_till_departure'] ?? '';
        $this->data['flight_type'] = $info['flight_type'] ?? '';
        $this->data['pnr'] = $info['pnr'] ?? '';
        $this->data['journey_from_to'] = $info['journey_from_to'] ?? '';
        $this->data['third_party_booking'] = $info['third_party_booking'] ?? '';
        $this->data['hotel_name'] = $info['hotel_name'] ?? '';
        $this->data['length_of_stay'] = $info['length_of_stay'] ?? '';
        $this->data['check_in_time'] = $info['check_in_time'] ?? '';
        $this->data['hotel_city'] = $info['hotel_city'] ?? '';
        $this->data['product_type'] = $info['product_type'] ?? '';
        $this->data['topup_number'] = $info['topup_number'] ?? '';
        $this->data['country_topup'] = $info['country_topup'] ?? '';

        $this->data['cart'] = $info['cart'] ?? '';
        $this->data['product_amount'] = $info['product_amount'] ?? '';
        $this->data['vat'] = $info['vat'] ?? '';
        $this->data['discount_amount'] = $info['discount_amount'] ?? '';
        $this->data['convenience_fee'] = $info['convenience_fee'] ?? '';

        return $this->data;
    }

    /**
     * Sets additional parameters for the payment request.
     * This can include optional fields like value_a,value_b etc.
     * 
     * @param array $info
     * @return array 
     */
    public function setAdditionalInfo(array $info)
    {
        $this->data['value_a'] = $info['value_a'] ?? '';
        $this->data['value_b'] = $info['value_b'] ?? '';
        $this->data['value_c'] = $info['value_c'] ?? '';
        $this->data['value_d'] = $info['value_d'] ?? '';

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
     * Sets the failed URL for redirecting after a failed payment.
     * 
     * @return void
     */
    protected function setFailedUrl()
    {
        $this->failed_url = rtrim(env('APP_URL'), '/') . $this->config['failed_url'];
    }

    /**
     * Gets the failed URL for redirecting after a failed payment.
     * 
     * @return string
     */
    protected function getFailedUrl()
    {
        return $this->failed_url;
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
