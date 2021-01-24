<?php

return [
    'module' => 'lakecms',
    'name' => 'cms模块',
    'introduce' => '简单高效方便的CMS系统模块',
    'author' => 'deatil',
    'authorsite' => 'http://github.com/deatil',
    'authoremail' => 'deatil@github.com',
    'version' => '1.0.0',
    'adaptation' => '2.0.2',
    
    'path' => '',
    
    // 依赖模块
    'need_module' => [
        ['lform', '2.0.2', '>=']
    ],
    
    // 设置
    'setting' => [],
    
    // 事件
    /*
    'event' => [
        [
            'name' => 'HttpRun',
            'class' => 'app\\lakecms\\behavior\\InitRoute',
            'description' => 'cms路由设置',
            'listorder' => 100,
            'status' => 1,
        ],
        [
            'name' => 'HttpRun',
            'class' => 'app\\lakecms\\behavior\\InitTemplate',
            'description' => 'cms模板配置',
            'listorder' => 100,
            'status' => 1,
        ],
    ],
    */
    
    // 菜单
    'menus' => include __DIR__ . '/menu.php',
    
    // 安装演示数据
    'demo' => 1,
];
