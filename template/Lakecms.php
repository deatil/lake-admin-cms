<?php

namespace app\lakecms\template;

use think\template\TagLib;

class Lakecms extends Taglib
{
    // 标签定义
    protected $tags = [
        /**
         * 标签定义： 
         * attr 属性列表 
         * close 是否闭合（0 或者1 默认1） 
         * alias 标签别名 
         * level 嵌套层次
         */
        'navbar' => [
            'attr' => '', 
            'close' => 0,
        ],
        'content' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 3,
        ],
        'prev' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 1,
        ],
        'next' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 1,
        ],
        'tag' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 3,
        ],
    ];

}
