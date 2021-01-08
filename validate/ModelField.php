<?php

namespace app\lakecms\validate;

use think\Validate;

/**
 * 模型字段
 *
 * @create 2020-1-7
 * @author deatil
 */
class ModelField extends Validate
{
    // 定义验证规则
    protected $rule = [
        'name' => 'require|regex:/^[a-zA-Z][A-Za-z0-9]+$/',
        'title' => 'require|chsAlphaNum',
        'show_type' => 'require|number',
        'status' => 'in:0,1',
    ];
    
    // 定义验证提示
    protected $message = [
        'name.require' => '字段名称不能为空',
        'name.regex' => '字段名称只能为字母和数字，并且仅能字母开头',
        'title.require' => '字段标题不能为空',
        'title.chsAlphaNum' => '模型名称只能为只能是汉字、字母、数字和下划线',
        'show_type.require' => '显示类型不能为空',
        'show_type.number' => '显示类型只能为数字',
        'status.in' => '字段状态格式错误',
    ];
    
    protected $scene = [
        'add' => [
            'name',
            'title',
            'show_type',
            'status',
        ],
        'edit' => [
            'name',
            'title',
            'show_type',
            'status',
        ],
    ];
}
