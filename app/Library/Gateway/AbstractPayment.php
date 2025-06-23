<?php

namespace App\Library\Gateway;

use Illuminate\Support\Facades\Http;

abstract class AbstractPayment implements PaymentInterface
{
    protected $api_url;
    protected $prefix;
    protected $username;
    protected $password;

    protected function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    protected function getPrefix()
    {
        return $this->prefix;
    }

    protected function setUsername($username)
    {
        $this->username = $username;
    }

    protected function getUsername()
    {
        return $this->username;
    }

    protected function setPassword($password)
    {
        $this->password = $password;
    }

    protected function getPassword()
    {
        return $this->password;
    }

    protected function setApiUrl($url)
    {
        $this->api_url = $url;
    }

    protected function getApiUrl()
    {
        return $this->api_url;
    }

    /**
     * @param $data
     * @param array $header
     * @param bool $setLocalhost
     * @return bool|string
     */
    public function callToApi($data, $header = [], $sendAsForm = false)
    {
        $url = $this->getApiUrl();

        $response = Http::withHeaders($header)->timeout(60);
        if ($sendAsForm) {
            $response = $response->asForm();
        }
        $response = $response->post($url, $data);

        if ($response->status() == 200) {
            return $response->json();
        } else {
            return "FAILED TO CONNECT WITH PAYMENT API";
        }
    }

    /**
     * @param $url
     * @param bool $permanent
     */
    public function redirect($url, $permanent = false)
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }
}
