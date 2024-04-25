# think-exception
exception for ThinkPHP 
## 安装
`composer require yangweijie/think-exception`

修改 app下provider

~~~
<?php
use think\exception\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
];
~~~

将 app.show_error_msg  设为 true

~~~
// 显示错误信息
'show_error_msg'   => true,
~~~

