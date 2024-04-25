<?php

namespace yangweijie\exception;

use think\facade\Config;

class ExceptionService extends \think\Service
{
    public function register()
    {
        $this->app->bind('think\exception\Handle', ExceptionHandle::class);
    }
    public function boot()
    {
        $app_config = Config::get('app');
        $tpl = __DIR__.'/tpl/think_exception.tpl';
        $app_config['exception_tmpl'] = $tpl;
        Config::set($app_config, 'app');
    }
}