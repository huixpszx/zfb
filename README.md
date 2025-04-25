# thinkphp秒级定时任务管理包

# 作用：
## Linux的定时任务管理，只能精确到每分钟，
## 此包针对php的thinkphp框架，需要精确到每秒钟的定时任务
## 如果执行途中遭遇错误，只会结束此次操作，不影响下一次任务执行
## 安装命令：tp项目下 composer require timespay/zfb


# 完整操作如下：

## 1.进入newtp项目安装 
 ### 如果没有tp项目，可以新建 composer create-project topthink/think newtp
 ### cd newtp
 ### composer require timespay/zfb  【会自动安装依赖 workerman/workerman】
 
  
## 2.将newtp/vendor/timespay/zfb/src/console
 ### 覆盖到newtp/config/console

## 3.进入newtp项目根目录，
###	php think timer start，测试开始
###	php think timer stop，测试结束

## 4.修改	
### 日常维护仅修改此文件即可，其他文件则无需修改
### 间隔秒数INTERVAL;
### 从app\thirdCode\NewTask获取实际任务,可自行修改实际任务路径

## 5.持久进程php think timer start --d
##  关闭持久进程php think timer stop --d