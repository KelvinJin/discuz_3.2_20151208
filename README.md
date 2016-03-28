## 关于 lab-pm
本分支用于研究 Discuz 3.x 的私信功能。

## 分支说明
* master 是原版本，不会变动
* dev- 开发版，用于实践开发
* lab- 研究版，用于研究学习
* stb- 稳定版，开发版完毕后的发布版

## 研究报告
\- pm 是基于 /home.php 入口来实现的  
\- pm 的路径是 /home.php?mod=space&do=pm  
\- 在 Discuz 中是通过 入口 -> 模块 -> 操作 这样方式来实现功能的;  
比较 ThinkPHP，ThinkPHP 则是 入口 -> 模块 -> 控制器 -> 操作 的方式来实现。
\- Discuz 的核心源码位于 source 目录下，比如 home.php 会首先调用这两个文件  

```
require_once './source/class/class_core.php';
require_once './source/function/function_home.php';
```

\- 控制结构相对比较严谨，通过 mod 和 do 来过滤，直接写入数组代码中;  
其实通过数据库来管理，在维护上更加方便一些  
\- Discuz! 的代码风格比较老旧，难以维护;  
比较多的步骤是集合在一段语句中的，而现代风则强调易读性  
\- Discuz! 的短信是通过 Ucenter 来实现的