<?php
/**
 * Created by PhpStorm.
 * User: King
 * Date: 2017/9/21
 * Time: 17:19
 */

namespace king\payment\driver\alipay;

use king\payment\base\Configure;
use king\payment\base\GatewayInterface;

abstract class Alipay implements GatewayInterface
{
    /**
     * 网关
     * @var string
     */
    public $gatewayUrl = "https://openapi.alipay.com/gateway.do";

    //返回数据格式
    public $format = "json";

    //api版本
    public $apiVersion = "1.0";

    // 表单提交字符集编码
    public $postCharset = "UTF-8";

    //签名类型
    public $signType = "RSA";

    protected $alipaySdkVersion = "alipay-sdk-php-20161101";

    // 支付宝全局配置参数
    protected $config;

    public $alipay_config;

    /**
     * getMethod
     * @auth King
     * @return mixed
     */
    abstract public function getMethod();

    /**
     * getProductCode
     * @auth King
     * @return mixed
     */
    abstract public function getProductCode();

    /**
     * Alipay constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        /*$config = [
            'app_id'               => '', // 应用ID,您的APPID。
            'merchant_private_key' => '', // 商户私钥
            'return_url'           => '', // 同步跳转
            'notify_url'           => '', // 异步通知地址
            'alipay_public_key'    => '', // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        ];*/

        $this->alipay_config = new Configure($config);

        $this->config = [
            'app_id'      => $this->alipay_config->get('app_id'),
            'version'     => $this->apiVersion,
            'format'      => $this->format,
            'sign_type'   => $this->signType,
            'method'      => '',
            'timestamp'   => date('Y-m-d H:i:s'),
            'alipay_sdk'  => $this->alipaySdkVersion,
            'charset'     => $this->postCharset,
            'return_url'  => $this->alipay_config->get('return_url', ''),
            'notify_url'  => $this->alipay_config->get('notify_url', ''),
            'biz_content' => '',
            'sign'        => '',
        ];
    }

    public function pay(array $config_biz)
    {
        $config_biz['product_code'] = $this->getProductCode();

        $this->config['method'] = $this->getMethod();
        $this->config['biz_content'] = json_encode($config_biz, JSON_UNESCAPED_UNICODE);
        $this->config['sign'] = $this->generateSign($this->config, $this->signType);
    }

    protected function generateSign($params, $signType = "RSA")
    {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function rsaSign($params, $signType = "RSA")
    {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function getSignContent(array $params, $verify = false)
    {
        ksort($params);

        $stringToBeSigned = "";
        foreach ($params as $k => $v) {
            if ($verify && $k != 'sign' && $k != 'sign_type') {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }

            if (!$verify && !$this->checkEmpty($v) && $k != 'sign' && '@' != substr($v, 0, 1)) {
                $stringToBeSigned .= $k . '=' . $v . '&';
            }
        }

        $stringToBeSigned = substr($stringToBeSigned, 0, -1);
        unset($k, $v);

        return $stringToBeSigned;
    }

    protected function sign($data, $signType = 'RSA')
    {
        $priKey = $this->alipay_config->get('merchant_private_key');
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        $sign = base64_encode($sign);

        return $sign;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * buildPayHtml
     * @auth King
     * @return string
     */
    public function buildPayHtml()
    {
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway."' method='POST'>";
        while (list($key, $val) = each($this->config)) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml .= "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml .= "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }

    /**
     * 检验值是否为空
     * checkEmpty
     * @auth King
     *
     * @param $value
     *
     * @return bool 为空返回true，不为空返回false
     */
    protected function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }

        if ($value === null) {
            return true;
        }

        if (trim($value) === '') {
            return true;
        }

        return false;
    }
}