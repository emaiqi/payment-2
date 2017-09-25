<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 15:48
 */

namespace king\payment;

use king\payment\base\Configure;
use king\payment\base\InvalidArgumentException;

class Payment
{
    private $config;

    private $drivers;

    private $gateways;

    public function __construct($config = [])
    {
        $this->config = new Configure($config);
    }

    public function driver($driver)
    {
        if (is_null($this->config->get($driver))) {
            throw new InvalidArgumentException('The Driver [' . $driver . ']\'s Config is not Defined.');
        }

        $this->drivers = $driver;

        return $this;
    }

    public function gateway($gateway)
    {
        if (!isset($this->drivers)) {
            throw new InvalidArgumentException('Driver is not Defined.');
        }

        $this->gateways = $this->createGateway($gateway);

        return $this->gateways;
    }

    protected function createGateway($gateway)
    {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . 'driver' . DIRECTORY_SEPARATOR . strtolower($this->drivers) . DIRECTORY_SEPARATOR . ucfirst($gateway) . 'Gateway.php';
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException('The Driver [' . $this->drivers . ']\'s Gateway [' . $gateway . '] is not Support.');
        }

        $gateway = __NAMESPACE__ . '\\driver\\' . strtolower($this->drivers) . '\\' . ucfirst($gateway) . 'Gateway';

        return $this->build($gateway);
    }

    protected function build($gateway)
    {
        return new $gateway($this->config->get($this->drivers));
    }
}
