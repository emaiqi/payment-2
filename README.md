# payment

使用说明
```
// 全局参数
$config = [
            'alipay' => [
                'app_id'               => '2013121100055554', // 应用ID,您的APPID。
                'merchant_private_key' => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAKK0PXoLKnBkgtOl0kvyc9X2tUUdh/lRZr9RE1frjr2ZtAulZ+Moz9VJZFew1UZIzeK0478obY/DjHmD3GMfqJoTguVqJ2MEg+mJ8hJKWelvKLgfFBNliAw+/9O6Jah9Q3mRzCD8pABDEHY7BM54W7aLcuGpIIOa/qShO8dbXn+FAgMBAAECgYA8+nQ380taiDEIBZPFZv7G6AmT97doV3u8pDQttVjv8lUqMDm5RyhtdW4n91xXVR3ko4rfr9UwFkflmufUNp9HU9bHIVQS+HWLsPv9GypdTSNNp+nDn4JExUtAakJxZmGhCu/WjHIUzCoBCn6viernVC2L37NL1N4zrR73lSCk2QJBAPb/UOmtSx+PnA/mimqnFMMP3SX6cQmnynz9+63JlLjXD8rowRD2Z03U41Qfy+RED3yANZXCrE1V6vghYVmASYsCQQCoomZpeNxAKuUJZp+VaWi4WQeMW1KCK3aljaKLMZ57yb5Bsu+P3odyBk1AvYIPvdajAJiiikRdIDmi58dqfN0vAkEAjFX8LwjbCg+aaB5gvsA3t6ynxhBJcWb4UZQtD0zdRzhKLMuaBn05rKssjnuSaRuSgPaHe5OkOjx6yIiOuz98iQJAXIDpSMYhm5lsFiITPDScWzOLLnUR55HL/biaB1zqoODj2so7G2JoTiYiznamF9h9GuFC2TablbINq80U2NcxxQJBAMhw06Ha/U7qTjtAmr2qAuWSWvHU4ANu2h0RxYlKTpmWgO0f47jCOQhdC3T/RK7f38c7q8uPyi35eZ7S1e/PznY=', // 商户私钥
                'return_url'           => '', // 同步跳转
                'notify_url'           => '', // 异步通知地址
                'alipay_public_key'    => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDIgHnOn7LLILlKETd6BFRJ0GqgS2Y3mn1wMQmyh9zEyWlz5p1zrahRahbXAfCfSqshSNfqOmAQzSHRVjCqjsAw1jyqrXaPdKBmr90DIpIxmIyKXv4GGAkPyJ/6FTFY99uhpiq0qadD/uSzQsefWo0aTvP/65zi3eof7TcZ32oWpwIDAQAB', // 支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            ]
        ];
        
// 业务参数 
$params = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject'      => 'test subject',
            .....
        ];
        
$payment = new Payment($config); 
$payment->driver('alipay')->gateway('web')->pay($params);

// 微信支付
$config = [
            'appid'      => '', // 应用ID,您的APPID。
            'mch_id'     => '', // 商户私钥
            'notify_url' => '', // 回调地址
            'key'        => '', // 商户支付密钥
            'ssl_cert'   => '', // 仅退款、撤销订单时需要
            'ssl_key'    => '', // 仅退款、撤销订单时需要
        ];
$params = [
            'body'             => '',   // 订单描述
            'out_trade_no'     => '',   // 商户订单号
            'total_fee'        => '',   // 订单金额，单位：分
            'spbill_create_ip' => '',   // 调用 API 服务器的 IP
        ];
$payment = new Payment($config); 
$payment->driver('wxpay')->gateway('scan')->pay($params);
```