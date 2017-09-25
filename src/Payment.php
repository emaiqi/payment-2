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

// 测试
$config = [
    'app_id'               => '', // 应用ID,您的APPID。
    'merchant_private_key' => '', // 商户私钥
    'return_url'           => '', // 同步跳转
    'notify_url'           => '', // 异步通知地址
    'alipay_public_key'    => '', // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
];

$payment = new Payment($config);

$params = [
    'out_trade_no' => time(),
    'total_amount' => '1',
    'subject'      => 'test subject',
];
$payment->driver('alipay')->gateway('web')->pay($params);
