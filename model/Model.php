<?php

namespace app\lakecms\model;

use think\Model as BaseModel;

use app\lakecms\support\Field as SupportField;
use app\lakecms\model\ModelField;

/**
 * 模型
 */
class Model extends BaseModel
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_model';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    public static function onBeforeInsert($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    public static function onBeforeUpdate($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
    }
    
    /**
     * 模型字段
     */
    public function fields()
    {
        return $this->hasMany(ModelField::class, 'modelid', 'id');
    }
    
    /**
     * 表单需要使用的模型字段
     */
    public static function formFields($where, $showType = 1)
    {
        $data = static::where($where)->find();
        $fields = $data['fields'];
        
        $data = collect($fields)
            ->order('sort', 'asc')
            ->map(function($item) use($showType, $data) {
                if ($item['status'] != 1) {
                    return null;
                }
                
                if ($item['show_type'] != 1) {
                    if ($item['show_type'] != $showType
                        || $item['show_type'] == 4
                    ) {
                        return null;
                    }
                }
                
                $type = $item['type'];
                
                // 格式数据
                if (empty($item['value'])) {
                    switch ($type) {
                        // 开关
                        case 'switch':
                            $item['value'] = 0;
                            break;
                        case 'checkbox':
                            $item['value'] = '';
                            break;
                    }
                } else {
                    if (is_array($item['value'])) {
                        $item['value'] = implode(',', $item['value']);
                    }
                    
                    switch ($type) {
                        // 开关
                        case 'switch':
                            $item['value'] = 1;
                            break;
                    }
                }
                
                return [
                    'type' => $item['type'],
                    'name' => $item['name'],
                    'title' => $item['title'],
                    'value' => $item['value'],
                    'options' => SupportField::parseAttr($item['options']),
                    'remark' => $item['remark'],
                    'ifrequire' => $item['is_must'],
                    'fieldArr' => 'modelField',
                ];
            })
            ->filter(function($item) {
                if (! empty($item)) {
                    return $item;
                }
            })
            ->values()
            ->toArray();
            
        return $data;
    }
    
    /**
     * 格式化字段信息
     */
    public static function formatFormFields($fields, $userData)
    {
        foreach ($fields as $field) {
            if (isset($userData[$field['name']])) {
                if ($field['type'] == 'date') {
                    $userData[$field['name']] = strtotime($userData[$field['name']]);
                }
                
                if ($field['type'] == 'datetime') {
                    $userData[$field['name']] = strtotime($userData[$field['name']]);
                }
                
                if ($field['type'] == 'Ueditor') {
                    $userData[$field['name']] = htmlspecialchars(stripslashes($userData[$field['name']]));
                }
            }
        }
        
        return $userData;
    }
    
    /**
     * 格式化显示字段信息
     */
    public static function formatFormShowFields($fields, $userData)
    {
        foreach ($fields as $field) {
            if (isset($userData[$field['name']])) {
                $data = $userData[$field['name']];
                if ($field['type'] == 'checkbox') {
                    $userData[$field['name']] = empty($data) ? [] : explode(',', $data);
                }
                
                if ($field['type'] == 'date') {
                    $userData[$field['name']] = empty($data) ? '' : date('Y-m-d', $data);
                }
                
                if ($field['type'] == 'datetime') {
                    $userData[$field['name']] = empty($data) ? '' : date('Y-m-d H:i:s', $data);
                }
                
                if ($field['type'] == 'Ueditor') {
                    $userData[$field['name']] = htmlspecialchars_decode($data);
                }
            }
        }
        
        return $userData;
    }
    
    /**
     * 格式化标签信息
     */
    public static function formatFormFieldTags($fields, $userData)
    {
        $tags = [];
        foreach ($fields as $field) {
            if (isset($userData[$field['name']])) {
                if ($field['type'] == 'tags') {
                    $array = preg_split('/[,;\r\n ]+/', trim($userData[$field['name']], ",;\r\n"));
                    $tags[] = $array;
                }
            }
        }
        
        return $tags;
    }
    
    /**
     * 表单验证的模型字段
     */
    public static function validateFields($where, $showType = 1)
    {
        $fields = ModelField::where($where)
            ->order("id ASC")
            ->select()
            ->toArray();
        
        $data = [];
        foreach ($fields as $field) {
            // 过滤不需要的字段
            if ($field['show_type'] != 1) {
                if ($field['show_type'] != $showType
                    || $field['show_type'] == 4
                ) {
                    continue;
                }
            }
            
            if (! empty($field['validate_rule'])) {
                $validateRules = SupportField::parseRule($field['validate_rule']);
            } else {
                $validateRules = [];
            }
            
            if ($field['is_must'] == 1) {
                $validateRules[] = 'require';
            }
            
            if (! empty($validateRules)) {
                $validateRule = implode('|', $validateRules);
                $data['rule'][$field['name'] . '|' . $field['title']] = $validateRule;
            }
            
            if (! empty($field['validate_message'])) {
                $validateMessages = SupportField::parseMessage($field['validate_message'] );
                foreach ($validateMessages as $key => $message) {
                    $data['message'][$field['name'].'.'.$key] = $message;
                }
            }
            
            if ($field['validate_time'] == 'always' 
                || $field['validate_time'] == 'create'
            ) {
                $data['scene']['create'][] = $field['name'];
            }
            if ($field['validate_time'] == 'always' 
                || $field['validate_time'] == 'update'
            ) {
                $data['scene']['update'][] = $field['name'];
            }
        }
        
        return $data;
    }
    
    /**
     * 检测规则
     */
    public function validateRules()
    {
        $validateRules = SupportField::parseRule($this->validate_rule);
        return $validateRules;
    }
    
    /**
     * 检测提示
     */
    public function validateMessages()
    {
        $validateMessages = SupportField::parseMessage($this->validate_rule);
        return $validateMessages;
    }

}
