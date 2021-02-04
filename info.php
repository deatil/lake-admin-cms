<?php

return [
    'module' => 'lakecms',
    'name' => 'cms模块',
    'introduce' => '简单高效方便的CMS系统模块',
    'author' => 'deatil',
    'authorsite' => 'http://github.com/deatil',
    'authoremail' => 'deatil@github.com',
    'version' => '1.0.2',
    'adaptation' => '2.3.27',
    
    // 模块地址
    'path' => __DIR__,
    
    // 依赖模块
    'need_module' => [
        ['lakead', '1.0.0', '>='],
        ['lform', '2.0.2', '>='],
        ['lfriendlink', '2.0.2', '>='],
    ],
    
    // 设置
    'setting' => [],
    
    // 事件
    'event' => [
        /*
        [
            'name' => 'HttpRun',
            'class' => 'app\\lakecms\\listener\\InitRoute',
            'description' => 'cms路由初始化',
            'listorder' => 100,
            'status' => 0,
        ],
        */
        [
            'name' => 'HttpRun',
            'class' => 'app\\lakecms\\listener\\InitTemplate',
            'description' => 'cms模板配置初始化',
            'listorder' => 100,
            'status' => 0,
        ],
    ],
    
    // 菜单
    'menus' => include __DIR__ . '/menu.php',
    
    // 安装演示数据
    'demo' => 1,
];
