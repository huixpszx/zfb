<?php

namespace Timespay\Zfb\demo;

use app\common\library\td74sxy\resRuku;
use app\common\logic\allinone\PubMethod;
use Exception;
use Timespay\Signrsa\Timespay;

class Zfb
{
    //  下单
    public static function order($flowData, $config, $type)
    {
        try {
            //  获取通用短连接
            $shortUrlPref = PubMethod::getShorUrl($flowData);
            //  获取通用Ip方法
            $client_ip = PubMethod::getClient_ip();
            Timespay::normal_dbg($client_ip,'ZFB-test');
            exit();
            //  提前通道需要的构造
            list($url, $orderNo, $amount, $orderInfo,$return_url) = demoZfb::brforegetParam($config, $flowData);
            //  构造通道规定的参数
            $param = demoZfb::getParam($orderNo, $config, $client_ip, $orderInfo, $type, $amount,$return_url);
            Timespay::normal_dbg($param, '构造参数');
            //  通道签名
            $sign = demoZfb::signypl(json_encode($param), $config['rsaPrivateKeyFilePath'], $config['password']);
            //  提交通道申请报文
            $request = demoZfb::http_post_json($url, json_encode($param), $sign, $config['sign_no']);
            Timespay::normal_dbg($request, __METHOD__);
            $rtn = [];
            //  申请得到通道回应
            return demoZfb::res($request, $rtn, $shortUrlPref, $flowData['flowid']);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Timespay::normal_dbg($msg, __METHOD__ . '报错');
        }
    }

    //  接受下单的支付回调
    public static function paycallback($channel_no)
    {
        $raw_post_data = file_get_contents('php://input', 'r');
        $config = RukuZfb::config($channel_no);
        Timespay::normal_dbg($raw_post_data, $config['name'] . '原始回调信息，json格式:');
        $res = json_decode($raw_post_data, true);
        $flowId = $res['outTradeNo'] ?? '';
        $msg = $res['payState'] ?? '';
        $status_up = $res['payState'] ?? '';//  给来的状态
        $code = ''; //  有些上游，状态和编码不是同一个字段
        $scc = '00';//  约定的成功
        $fail = '2';//  失败的规定，如果没有就乱写
        $dbg_name = $channel_no . '支付回调';
        $mess = '{"returnCode":"0000"}'; //   上游规定的回应
        $userid = $res['buyerId'] ?? '';//  支付宝id号，做黑名单
        $third_num = $res['transactionNo']??'';//   三方订单号
        $res = resRuku::updateQueryPay($msg, $status_up, $scc, $fail, $code,
            $flowId, $dbg_name, $mess,$userid,$third_num);
        if ($res) {
            Timespay::normal_dbg($channel_no . '接收平台回调，处理成功。');
        }
        return $mess;
    }

}