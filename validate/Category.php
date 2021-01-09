<?php

namespace app\lakecms\validate;

use think\Validate;

/**
 * 栏目
 *
 * @create 2020-1-7
 * @author deatil
 */
class Category extends Validate
{
    // 定义验证规则
    protected $rule = [
        'parentid|上级栏目' => 'require|number',
        'modelid|所属模型' => 'require|number',
        'name|栏目标识' => 'require|alphaNum',
        'title|栏目标题' => 'require|chsAlphaNum',
        'type|栏目类型' => 'require|in:1,2',
        'sort|栏目排序' => 'require|number',
        'status|栏目状态' => 'require|in:0,1',
    ];

    // 定义验证提示
    protected $message = [
        'modelid.number' => '所属模型不能为空',
    ];

    protected $scene = [
        'add' => [
            'parentid', 
            'modelid', 
            'name', 
            'title', 
            'type', 
            'sort', 
            'status'
        ],
        'edit' => [
            'parentid', 
            'name', 
            'title', 
            'sort', 
            'status'
        ],
    ];
}
