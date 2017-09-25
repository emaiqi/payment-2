<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 17:21
 */

namespace king\payment\driver\alipay;


class WebGateway extends Alipay
{
    public function getMethod()
    {
        return 'alipay.trade.pay';
    }

    public function getProductCode()
    {
        return 'FAST_INSTANT_TRADE_PAY';
    }

    public function pay(array $config_biz)
    {
        parent::pay($config_biz);

        return $this->buildPayHtml();
    }


}