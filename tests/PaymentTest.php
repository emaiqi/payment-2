<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 17:56
 */

namespace king\payment\tests;

use king\payment\base\GatewayInterface;
use king\payment\base\InvalidArgumentException;
use king\payment\Payment;

class PaymentTest extends TestCase
{
    public function testDriverWithoutConfig()
    {
        $this->expectException(InvalidArgumentException::class);
        $payment = new Payment([]);
        $payment->driver('alipay');
    }

    public function testDriver()
    {
        $payment = new Payment(['alipay' => ['app_id' => 'bb']]);
        $result = $payment->driver('alipay');

        $this->assertInstanceOf(Payment::class, $result);
    }

    public function testGateway()
    {
        $payment = new Payment(['alipay' => ['app_id' => 'bb']]);
        $result = $payment->driver('alipay')->gateway('web');

        $this->assertInstanceOf(GatewayInterface::class, $result);
    }

    public function testGateWithoutDriver()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Driver is not Defined.');

        $payment = new Payment();
        $payment->gateway('foo');
    }

    public function testInvalidGateway()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Driver [alipay]\'s Gateway [foo] is not Support.');

        $payment = new Payment(['alipay' => ['app_id' => 'bb']]);
        $payment->driver('alipay')->gateway('foo');
    }

    public function testPayment()
    {
        // 测试
        $config = [
            'alipay' => [
                'app_id'               => '2013121100055554', // 应用ID,您的APPID。
                'merchant_private_key' => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAKK0PXoLKnBkgtOl0kvyc9X2tUUdh/lRZr9RE1frjr2ZtAulZ+Moz9VJZFew1UZIzeK0478obY/DjHmD3GMfqJoTguVqJ2MEg+mJ8hJKWelvKLgfFBNliAw+/9O6Jah9Q3mRzCD8pABDEHY7BM54W7aLcuGpIIOa/qShO8dbXn+FAgMBAAECgYA8+nQ380taiDEIBZPFZv7G6AmT97doV3u8pDQttVjv8lUqMDm5RyhtdW4n91xXVR3ko4rfr9UwFkflmufUNp9HU9bHIVQS+HWLsPv9GypdTSNNp+nDn4JExUtAakJxZmGhCu/WjHIUzCoBCn6viernVC2L37NL1N4zrR73lSCk2QJBAPb/UOmtSx+PnA/mimqnFMMP3SX6cQmnynz9+63JlLjXD8rowRD2Z03U41Qfy+RED3yANZXCrE1V6vghYVmASYsCQQCoomZpeNxAKuUJZp+VaWi4WQeMW1KCK3aljaKLMZ57yb5Bsu+P3odyBk1AvYIPvdajAJiiikRdIDmi58dqfN0vAkEAjFX8LwjbCg+aaB5gvsA3t6ynxhBJcWb4UZQtD0zdRzhKLMuaBn05rKssjnuSaRuSgPaHe5OkOjx6yIiOuz98iQJAXIDpSMYhm5lsFiITPDScWzOLLnUR55HL/biaB1zqoODj2so7G2JoTiYiznamF9h9GuFC2TablbINq80U2NcxxQJBAMhw06Ha/U7qTjtAmr2qAuWSWvHU4ANu2h0RxYlKTpmWgO0f47jCOQhdC3T/RK7f38c7q8uPyi35eZ7S1e/PznY=', // 商户私钥
                'return_url'           => '', // 同步跳转
                'notify_url'           => '', // 异步通知地址
                'alipay_public_key'    => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIgHnOn7LLILlKETd6BFRJ0GqgS2Y3mn1wMQmyh9zEyWlz5p1zrahRahbXAfCfSqshSNfqOmAQzSHRVjCqjsAw1jyqrXaPdKBmr90DIpIxmIyKXv4GGAkPyJ/6FTFY99uhpiq0qadD/uSzQsefWo0aTvP/65zi3eof7TcZ32oWpwIDAQAB', // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            ]
        ];

        $payment = new Payment($config);

        $params = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject'      => 'test subject',
        ];
        $res = $payment->driver('alipay')->gateway('web')->pay($params);

    }
}