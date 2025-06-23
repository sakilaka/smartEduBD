<?php

namespace App\Library\Gateway;

interface PaymentInterface
{
    public function makePayment(array $data);

    public function setParams($data);

    public function paymentValidate($order_id);

    public function setRequiredInfo(array $data);

    public function setCustomerInfo(array $data);

    public function setShipmentInfo(array $data);

    public function setProductInfo(array $data);

    public function setAdditionalInfo(array $data);

    public function callToApi($data, $header = []);
}
