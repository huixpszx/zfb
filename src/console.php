<?php
/**
 * 控制台配置
 */

use Timespay\Zfb\command\SelfTimer;

return [
    // 指令定义
    'commands' => [
        'timer' => SelfTimer::class
    ],
];