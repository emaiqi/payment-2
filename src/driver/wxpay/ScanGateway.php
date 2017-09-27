<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/26
 * Time: 14:47
 */

namespace king\payment\driver\wxpay;

class ScanGateway extends Wxpay
{
    protected function getTradeType()
    {
        return 'NATIVE';
    }

    public function pay(array $config_biz)
    {
        return $this->unifiedOrder($config_biz)['code_url'];
    }
}