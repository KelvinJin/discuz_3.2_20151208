## 关于 lab-G
本项目是为了研究 Discuz! 3.x 的大 G 功能  
G 函数是 Discuz 的通用函数，请访问 /lab-g 目录来查看所建立的文件

## 实验步骤
我在根目录(/)，实验目录(/lab-g)和文件中放置了 var_dump($_G) 
和核心文件的 include 操作 

> require_once './source/class/class_core.php';

观察对 $_G 和 $_SESSION 的影响。

## 研究报告
\- $_G 变量中包含了大量通用信息，包括用户名，时间，系统信息等
\- $_G 需要通过 C::app()->init 来渲染，要不然只是一些信息结构的默认设置，与所在系统无法互动
\- Discuz 主要通过 $_COOKIE 来登陆确认，而非 $_SESSION

## 分支说明
* master 是原版本，不会变动
* dev- 开发版，用于实践开发
* lab- 研究版，用于研究学习
* stb- 稳定版，开发版完毕后的发布版