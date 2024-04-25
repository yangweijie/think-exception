# think-exception
exception for ThinkPHP 
## 安装
`composer require yangweijie/think-exception`

.env 里 app_debug=true

将 app.show_error_msg  设为 true

~~~
// 显示错误信息
'show_error_msg'   => true,
~~~

## 配置设置 exception.php里 editor

~~~
 // 当前编辑器
'editor' => 'vscode',
~~~