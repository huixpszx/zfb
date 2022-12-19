<?php

namespace Timespay\Zfb\demo;

use app\common\logic\dbg;

class demoZfb
{
    public static function signypl($data, $rsaPrivateKeyFilePath, $password)
    {
        $certs = array();
        openssl_pkcs12_read(file_get_contents($rsaPrivateKeyFilePath), $certs, $password); //其中password为你的证书密码

        ($certs) or die('请检查RSA私钥配置');

        openssl_sign($data, $sign, $certs['pkey'], OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
    }

    public static function http_post_json($url, $jsonStr, $sign, $sign_no)
    {
        $ch = curl_init();
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr),
            'x-efps-sign-no:' . $sign_no,
            'x-efps-sign-type:SHA256withRSA',
            'x-efps-sign:' . $sign,
            'x-efps-timestamp:' . date('YmdHis'),
        );
        dbg::duijie([$url, $headers, $jsonStr], '申请报文');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 跳过检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 跳过检查
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return array($httpCode, $response);
    }

    /**
     * @param $config
     * @param $flowData
     * @return array
     */
    public static function brforegetParam($config, $flowData): array
    {
        $url = $config['url'] . $config['order'];
        $orderNo = $flowData['flowid'] ?? '';
        $amount = number_format($flowData['money_order'], 2, ".", "") * 100;//强制两位小数金额
        $orderInfo = array();
        $orderInfo['Id'] = $orderNo;
        $orderInfo['businessType'] = '130001';
        $orderInfo['goodsList'] = array(array('name' => 'pay', 'number' => 'one', 'amount' => 1));
        $return_url = $flowData['succ_back']??$config['return_url'];
        return array($url, $orderNo, $amount, $orderInfo,$return_url);
    }

    /**
     * @param $orderNo
     * @param $config
     * @param $client_ip
     * @param $orderInfo
     * @param $type
     * @param $amount
     * @return array
     */
    public static function getParam($orderNo, $config, $client_ip, $orderInfo, $type, $amount,$return_url): array
    {
        $param = array(
            'outTradeNo' => $orderNo,
            'customerCode' => $config['appid'],
            'clientIp' => $client_ip,
            'orderInfo' => $orderInfo,
            'payMethod' => $type,
            'payAmount' => $amount,
            'payCurrency' => 'CNY',
            'channelType' => '02',
            'notifyUrl' => $config['payNotifyUrl'],
            'redirectUrl' => $return_url,
            'transactionStartTime' => date('YmdHis'),
            'nonceStr' => 'pay' . rand(100, 999),
            'version' => '3.0'
        );
        return $param;
    }

    /**
     * @param array $request
     * @param array $rtn
     * @param $shortUrlPref
     * @param $flowid
     * @return array|void
     */
    public static function res(array $request, array $rtn, $shortUrlPref, $flowid)
    {
        if ($request && $request[0] == 200) {
            $re_data = json_decode($request[1], true);
            if ($re_data['returnCode'] == '0000') {
                $rtn['errcode'] = 'success'; //入库成功标志的值，此处success不变
                $rtn['err_status'] = '0'; //入库成功标志的值，此处success不变
                $rtn['pay_url'] = $shortUrlPref . $flowid; //短连接打开
                $rtn['qr_url'] = $re_data['codeUrl'] ?? ''; //返回给渠道的值，结果取决于通道文档规定的值
            } else {
                $msg = $re_data['returnMsg'] ?? '';
                $rtn['errmsg'] = '上游告知：' . $msg . '，直接联系上游解决。';
                exit($rtn['errmsg']);
            }
            return $rtn;
        }
    }


}