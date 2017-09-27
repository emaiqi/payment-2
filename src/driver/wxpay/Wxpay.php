<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/26
 * Time: 14:46
 */

namespace king\payment\driver\wxpay;


use king\payment\base\Configure;
use king\payment\base\GatewayException;
use king\payment\base\GatewayInterface;
use king\payment\base\InvalidArgumentException;
use king\payment\traits\HttpRequest;

abstract class Wxpay implements GatewayInterface
{
    use HttpRequest;

    /**
     * @var string
     */
    private $gateway_unifiedorder = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Configure
     */
    private $wxpay_config;

    /**
     * getTradeType
     * @auth King
     * @return mixed
     */
    abstract protected function getTradeType();

    public function __construct(array $config)
    {
        /*$config = [
            'appid'      => '', // 应用ID,您的APPID。
            'mch_id'     => '', // 商户私钥
            'notify_url' => '', // 回调地址
            'key'        => '', // 商户支付密钥
            'ssl_cert'   => '', // 仅退款、撤销订单时需要
            'ssl_key'    => '', // 仅退款、撤销订单时需要
        ];*/

        $this->wxpay_config = new Configure($config);

        $this->config = [
            'appid'      => $this->wxpay_config->get('appid'),
            'mch_id'     => $this->wxpay_config->get('mch_id'),
            'nonce_str'  => $this->generateNonceStr(),
            'sign_type'  => 'MD5',
            'trade_type' => $this->getTradeType(),
            'notify_url' => $this->wxpay_config->get('notify_url', ''),
            'sign'       => '',
        ];
    }

    /**
     * unifiedOrder
     * @auth King
     *
     * @param array $params
     *
     * @return mixed
     */
    public function unifiedOrder(array $params)
    {
        $this->config = array_merge($this->config, $params);

        return $this->getResult($this->gateway_unifiedorder);
    }

    /**
     * getResult
     * @auth King
     *
     * @param      $gateway
     * @param bool $cert
     *
     * @return mixed
     * @throws GatewayException
     */
    protected function getResult($gateway, $cert = false)
    {
        $this->config['sign'] = $this->generateSign();

        if ($cert) {
            $result = $this->post($gateway, $this->toXml(), [
                'cert'    => $this->wxpay_config->get('ssl_cert', ''),
                'ssl_key' => $this->wxpay_config->get('ssl_key', ''),
            ]);
        } else {
            $result = $this->post($gateway, $this->toXml());
        }

        $data = $this->fromXml($result);
        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'error: ' . $data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - ' . $data['err_code_des'] : '';
        }

        if (!isset($error) && $this->generateSign() !== $data['sign']) {
            $error = 'error: return data sign error';
        }

        if (isset($error)) {
            throw new GatewayException($error, 1000, $data);
        }

        return $data;
    }

    protected function generateSign()
    {
        return $this->sign();
    }

    protected function sign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->config);
        $string = $this->getSignContent();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->wxpay_config->get('key');
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);

        return $result;
    }

    /**
     * getSignContent
     * @auth King
     * @return string
     */
    protected function getSignContent()
    {
        $buff = "";
        foreach ($this->config as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");

        return $buff;
    }

    /**
     * toXml
     * @auth King
     * @return string
     */
    protected function toXml()
    {
        if (!is_array($this->config) || count($this->config) <= 0) {
            throw new InvalidArgumentException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->config as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }

    public function fromXml($xml)
    {
        if (!$xml) {
            throw new InvalidArgumentException("xml数据异常！");
        }

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);

        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    protected function generateNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

}