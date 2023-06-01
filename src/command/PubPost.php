<?php

namespace Timespay\Zfb\command;
class PubPost
{
    /** sent from格式，即是字符串格式  **/
    public static function send_post_from($url, $post_data, $time = '10')
    { //POST FROM格式，即是传字符串，不是传json格式
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded;charset=UTF-8',
                'content' => $postdata,
                'timeout' => $time
            )
        );
        $content = stream_context_create($options);
        $result = file_get_contents($url, false, $content);
        return iconv("utf-8", "utf-8//IGNORE", $result);
    }

    /**
     * GET请求
     * @param $url
     * @param array $headers
     * @param array $cookies
     * @return bool|string
     */
    public static function httpGet($url, $headers = [], $cookies = [])
    {
        $httpOptions = array(
            'method' => 'GET',
            'timeout' => 10,
        );

        if ($cookies) {
            $ls = [];
            foreach ($cookies as $k => $v) {
                $ls[] = $k . '=' . $v;
            }
            $headers['Cookie'] = implode('; ', $ls);
        }


        if ($headers) {
            $ls = [];
            foreach ($headers as $k => $v) {
                $ls[] = $k . ': ' . $v;
            }
            $httpOptions['header'] = $ls;
        }

        $options = array(
            'http' => $httpOptions
        );
        $context = stream_context_create($options);
        return @file_get_contents($url, false, $context);
    }
}