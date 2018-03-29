<?php
//手机快捷支付工具类
namespace Pay;
class Core
{
    private $config = array(
        "version" => "1.0.0",
        "transCode" => "8888", #8888普通交易 6666 担保交易
        "merchantId" => "",
        "merOrderNum" => "",
        "bussId" => "",
        "tranAmt" => "",
        "sysTraceNum" => "",
        "tranDateTime" => "",
        "currencyType" => "156",
        "merURL" => "",
        "backURL" => "",
        "orderInfo" => "",
        "userId" => "",
        "userIp" => "",
        "bankId" => "",
        "stlmId" => "",
        "entryType" => "",
        "reserver1" => "",
        "reserver2" => "",
        "reserver3" => "",
        "reserver4" => "",
        "signValue" => "",
        "datakey" => "Z46lud72C6004442",
        "getWay" => "http://cashier.etonepay.com/NetPay/BankSelect.action",#支付地址
    );

    public function __construct($config)
    {
        $this->config = array_merge($config);
    }

    private function setConfig($key, $value)
    {
        $this->config[$key] = $value;
    }

    private function buildSign()
    {
        $build = array("version", "transCode", "merchantId", "merOrderNum", "bussId", "tranAmt", "sysTraceNum", "tranDateTime", "currencyType", "merURL", "backURL", "orderInfo", "userId");
        $arr = array();
        foreach ($build as $val) {
            array_push($arr, $this->config[$val]);
        }
        $signStr = implode("|", $arr) . $this->config["datakey"];


        return md5($signStr);
    }

    public function toPay()
    {
        self::setConfig("signValue", self::buildSign());


        $param = "version,transCode,merchantId,merOrderNum,bussId,tranAmt,sysTraceNum,tranDateTime,currencyType,merURL,orderInfo,bankId,stlmId,userId,userIp,backURL,reserver1,reserver2,reserver3,reserver4,entryType,signValue";
        echo "<html><body>";
        echo "<form action='" . $this->config['getWay'] . "' method='post'>";
        echo "<input type='submit' value='11111111'>";

        foreach ($this->config as $key => $val) {
            if (strstr($param, $key) != "") {

                echo "<input name='" . $key . "' type='hidden' value='" . $val . "'>";
            }
        }

        echo "</form>";
        echo "</body></html>";

    }

    public function toPay2()
    {
        self::setConfig("signValue", self::buildSign());


        $param = "version,transCode,merchantId,merOrderNum,bussId,tranAmt,sysTraceNum,tranDateTime,currencyType,merURL,orderInfo,bankId,stlmId,userId,userIp,backURL,reserver1,reserver2,reserver3,reserver4,entryType,signValue";
        $arr = array();
        foreach ($this->config as $key => $val) {
            if (strstr($param, $key) != "") {
                $arr[$key] = $val;
            }
        }

        $url = "http://cashier.etonepay.com/NetPay/BankSelect.action";

        $post_data = $arr;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);

        //打印获得的数据
        dump($output);
        dump($arr);
        die;
    }


    public function CheckSign($data)
    {
        $result["orderId"] = isset($data['merOrderNum']) ? $data['merOrderNum'] : 0;
        $orderid = explode("ds", $result["orderId"]);
        $result["orderId"] = "PAY" . $orderid[0];
        $result["OrderAmt"] = isset($data['orderAmt']) ? ($data['orderAmt'] / 100) : 0;
        $result["tranAmt"] = isset($data['tranAmt']) ? $data['tranAmt'] / 100 : 0;
        $result["uid"] = $orderid[1];;
        $result["code"] = 0;
        if (str_replace('0000', 'dongsheng', $data['respCode']) != 'dongsheng') {
            return $result;
        }
        $parm = array("transCode", "merchantId", "respCode", "sysTraceNum", "merOrderNum", "orderId", "bussId", "tranAmt", "orderAmt", "bankFeeAmt", "integralAmt", "vaAmt", "bankAmt", "bankId", "integralSeq", "vaSeq", "bankSeq", "tranDateTime", "payMentTime", "settleDate", "currencyType", "orderInfo", "userId");
        $sign = array();
        foreach ($parm as $val) {
            array_push($sign, $data[$val]);
        }
        $signStr = md5(implode("|", $sign) . $this->config['datakey']);
        $signStr == $data['signValue'] && $result["code"] = 1;
        //echo "<pre>";
        //print_r($result);die;
        return $result;
    }
}

?>