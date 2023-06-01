<?php
declare (strict_types=1);


namespace Timespay\Zfb\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;


class Timer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\timer')
            ->setDescription('the app\command\timer command');
    }


    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('app\command\timer');
    }
}