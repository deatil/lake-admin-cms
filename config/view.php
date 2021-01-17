<?php

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type' => 'Think',
    // 模板路径
    'view_path' => '',
    // 模板后缀
    'view_suffix' => 'html',
    // 模板输出替换
    'tpl_replace_string' => [
        '__STATIC__' => '/static',
        '__MODULES_STATIC__' => '/static/modules',
        '__UPLOAD__' => '/uploads',
    ],
    // 模板文件名分隔符
    'view_depr' => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin' => '{',
    // 模板引擎普通标签结束标记
    'tpl_end' => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end' => '}',
    // 标签库
    'taglib_build_in' => 'cx,app\lakecms\template\taglib\Lakecms',
];
