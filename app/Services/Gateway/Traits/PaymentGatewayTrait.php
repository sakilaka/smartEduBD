<?php

namespace App\Services\Gateway\Traits;

use Illuminate\Support\Facades\Config;

trait PaymentGatewayTrait
{
    /**
     * @var string The config file path for the gateway.
     */
    private $config_file;

    /**
     * Set username and password for the gateway credentials.
     *
     * @param object $gateway 
     * @return bool
     */
    protected function setUsernameAndPassword($gateway)
    {
        $this->setConfigFile($gateway->provider);
        if (!$this->config_file || config("$this->config_file.testmode"))  return false;

        $credientials = collect($gateway->options)->where('gateway', $gateway->provider)->first();

        Config::set("$this->config_file.credentials.username", $credientials['username'] ?? '');
        Config::set("$this->config_file.credentials.password", $credientials['password'] ?? '');
        Config::set("$this->config_file.prefix", $credientials['prefix'] ?? '');

        $this->config = config($this->config_file);
        return config($this->config_file);
    }

    /**
     * Sets the config file based on the provided payment method.
     *
     * @param string $method 
     */
    private function setConfigFile(string $method)
    {
        switch ($method) {
            case 'SSL':
                $this->config_file = 'sslcommerz';
                break;

            case 'SPAY':
                $this->config_file = 'shurjopay';
                break;

            default:
                $this->config_file = '';
                break;
        }
    }
}
