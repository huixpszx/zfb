<?php

namespace Timespay\Zfb\demo;

use app\common\logic\allinone\PubMethod;
use app\common\logic\dbg;
use think\facade\Config;

class RukuZfb
{

    public static function config($channel_no)
    {
        return Config::get('zfb.pubZfb') + Config::get('zfb.' . $channel_no);
    }

    public static function resOrder($resObj)
    {
        $redirectUrl = $resObj['redirectUrl'] ?? $resObj['cause'] ?? '';//这里的code就是支付
        return $redirectUrl;
    }

    public static function resOrderPayReturn($timeStart, $dbg_name, $flowId, $flowData, $html)
    {
        $rtn = $flowData;
        //   $shortUrlPref = str_replace(' ', '', Config::get('shortUrlPref'));
        $htmlUrl = str_replace(' ', '', Config::get('shortHtmlPref'));
        dbg::testdbg($htmlUrl, 'html2url');
        if (true) {
            $rtn['errcode'] = 'success'; //入库成功标志的值，此处success不变
            $rtn['err_status'] = '0'; //入库成功标志的值，此处success不变
            $rtn['pay_url'] = $htmlUrl . $flowId; //短连接打开
            $rtn['html_url'] = $html; //返回给渠道的值，结果取决于通道文档规定的值
        }
        PubMethod::extractedDbg($timeStart, $dbg_name, $flowId);
        return $rtn;
    }
}