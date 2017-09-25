<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 17:16
 */

namespace king\payment\base;


interface GatewayInterface
{
    public function pay(array $config_biz);
}