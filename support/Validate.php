<?php

namespace app\lakecms\support;

use think\Validate as BaseValidate;

/**
 * 验证
 *
 * @create 2020-1-13
 * @author deatil
 */
class Validate extends BaseValidate
{
    // 验证规则
    protected $rule = [];
    
    // 验证提示
    protected $message = [];
    
    // 场景
    protected $scene = [];
    
    /**
     * 设置规则
     */
    public function withRules($rule)
    {
        $this->rule = $rule;
        return $this;
    }
    
    /**
     * 设置验证提示
     */
    public function withMessages($message)
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * 设置场景
     */
    public function withScenes($scene)
    {
        $this->scene = $scene;
        return $this;
    }
}
