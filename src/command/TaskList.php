<?php

namespace Timespay\Zfb\command;

use app\thirdCode\NewTask;

class TaskList
{
    //	此处修改间隔的秒数
    const INTERVAL = 5;

    //	此处修改真实任务
    public static function AddNewTask(){
        {
            // 从app\thirdCode\NewTask获取实际任务
            // 可自行修改实际任务路径
            NewTask::AddNewTask();
        }
    }
}