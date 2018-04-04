<?php
/**
 * Created by PhpStorm.
 * User: nightdays
 * Date: 18-3-30
 * Time: 上午8:44
 */

namespace app\index\controller;

use think\Controller;
use think\Db;
use Pay\AlipayService;
class Pay{
    public function insu_pay(){
        $returnUrl = url('Pay/url_reut');
        header('Content-type:text/html; Charset=utf-8');
        $appid = '2018032902469247';  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $notifyUrl = 'http://www.xxx.com/alipay/notify.php';     //付款成功后的异步回调地址
        $outTradeNo = uniqid();     //你自己的商品订单号
        $payAmount = 0.01;          //付款金额，单位:元
        $orderName = '支付测试';    //订单标题
        $signType = 'RSA2';       //签名算法类型，支持RSA2和RSA，推荐使用RSA2
//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        $saPrivateKey= 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCwXv32bVSJCt4NbQLZ3S9l6NURdCH3+dns9HQI1rgPexsSSfHyVRqDFbMqQ6eg33cidVBXY9lfBOU8xK3Fxalbh+A/A2SwzCR+pIBy1ua6RsQXwGolzzL563DtCRQUAJPOkQsZiW4cIGB5BmNPrKsbh78zz4OjshZWT1pdgH5/SWvPbFQNNc2v3BayVCNN15LknB97qOk5QXaIyTFA3LoDSKu9Yx9xvd/5G+LJjApJyc8Hz7AM4MMilthuhceTKZfJEQTkfWPrjOqT2hKsMTrey/8tXsJ/xAkobdfndxpwKF6rUTMWOln75+GewSTeEGAQmcrEc5Z9blGOSvBy7TIdAgMBAAECggEARKKy8lNZsnsoLtFdaqtI5r/VKxWmonn49N2sykPFHUErJ9Le9Q9pYdnv/2KUuWY9VouQ2HFjBjdBfoSLm4twfM5D1kHbzXGkQiSCWC6JW7RuW0/l/xDDJb8aEySVS7Nt0dVgHG//CbMP2AlEXJ01eWE6Z086S1nil3QKFpkKoT2pNpy1IsxtgGfL69nABYBRKeFyaUaC6nB1/OJIlDFdITLqCbsB3Xq+9ac7GZv3sTzpr0ZgZmySilLxHjciZXchQ+jDy7lRrLrjmDqFyWJJMt25Emi3hp2SigPlU2IZE+OdliI82k6x0j8cUmOysk4oJoEXM35T25i1PXb2milFgQKBgQD+3vYNYs3UEglJ9qmOcBHIeigZPSlphSWms6mxj5uzqETxes35uSqOqH6mV4ISvh2KMN4RRkhzsyo4QqkJBRXMYHLf5PAoC8lqPXYQstBJGxY5Vv9+cwBF1kxXJv9Xw5RQIPC0gzyzeAO6K4qtoCDTZq7OUKztrCOlpfFr4sm+1wKBgQCxJwHhz/isuEUGWkjQ8GYqWocDPuPp2iMx/LVyTCKoYKKca/ekB6AzmXcqcxea5/kWdr4R9a6LRUqfrYvLb/L20jGAA8Gdx95RkidR6jN30M9ZbfZ0E56Sa+xZpwSk5xIdYPF9ALoTZOK5Geruglf/IFmA5zGL+Fo91UavwPZ8KwKBgQCqP0L6pBI7Y6TizCpso/2s/bRFT66W9sK0vPRQUE5ATtlmuJJYClEcI/8dm/yeCnJu+b+MqHcDGh3MoPKKvOPXtmFln2awSVdMaSuxcdBh1P8eZDecPrNi8wfhe4I7HMC4WZiP684jT7qlpCopWnoy3DdOR2OMC11wbA189Guw1wKBgQCW19wmZ2wbxhwgOKhgIRMdZVApV1fCPFhTrBbkaCfqr75G6zhGl6e1yCtMHgwvfu7+TcWyjTw+opXUOXYbmAuOV5SGqKwoqZOIaabJfV6t20NzpsqANGSF0RfDjw/JSmiGU5HNGt1WvkS/0G3XAW3kc6tCs5ng0Wwl0oEKDojTWQKBgQCwNLiWjSK/+9OFD+L593ziEQv8W3qtG5pb+dsl0yA5YJ4D5Iupi9Yt1L0eezCXf7FBNogic0r9phzx8s3Im7edvX2ufjFVtEFmmHsDUbiW7v2IeZNBQNCluGxlGFhxcIym8UEMqft1Nl3eVbRFo15WpOggi5DYqfUR+19drC1gOA==';

        $aliPay = new AlipayService($appid,$returnUrl,$notifyUrl,$saPrivateKey);

        $result = $aliPay->doPay($payAmount,$outTradeNo,$orderName,$returnUrl,$notifyUrl);

        $result = $result['alipay_trade_precreate_response'];
        if($result['code'] && $result['code']=='10000'){
            //生成二维码
            $url = 'http://pan.baidu.com/share/qrcode?w=300&h=300&url='.$result['qr_code'];
            echo "<img src='{$url}' style='width:300px;'><br>";
            echo '二维码内容：'.$result['qr_code'];
        }else{
            echo $result['msg'].' : '.$result['sub_msg'];
        }

    }
    public function url_reut(){

    }
}


