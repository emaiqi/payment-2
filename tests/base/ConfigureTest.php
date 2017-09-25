<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/22
 * Time: 11:03
 */

namespace king\payment\tests\base;


use king\payment\base\Configure;
use king\payment\tests\TestCase;

class ConfigureTest extends TestCase
{
    public function testSet()
    {
        $configure = new Configure();
        $arr = $configure->set('alipay', ['app_id' => '123456789']);

        $this->assertArrayHasKey('alipay', $arr);
    }

    public function testGet()
    {
        $arr = [
            'foo' => 'bar',
            'alipay' => [
                'app_id' => '123456789'
            ]
        ];

        $configure = new Configure($arr);

        $this->assertTrue(isset($configure['foo']));
        $this->assertSame($arr, $configure->get());
        $this->assertSame('bar', $configure->get('foo'));

        $this->assertEquals($configure->get('alipay.app_id'), '123456789');
    }
}