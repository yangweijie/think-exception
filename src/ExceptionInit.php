<?php
namespace yangweijie\exception;
use think\App;
class ExceptionInit{
    public function handle(App $app){
        $tpl = __DIR__.'/tpl/think_exception.tpl';
        $app_config = $app->config->get('app');
        $app_config['exception_tmpl'] = $tpl;
        $app->config->set($app_config, 'app');
    }
}