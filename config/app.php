<?php

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // URL普通方式参数 用于自动生成
    'url_common_param' => true,

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',

    // 异常页面的模板文件
    'exception_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => false,
];
