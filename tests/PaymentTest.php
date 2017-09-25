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
}