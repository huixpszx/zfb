<?php
namespace Timespay\Zfb;

use Exception;

class Zfbpay
{
    public static function test($text = 'test-ok')
    {
        try {
            return $text;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}