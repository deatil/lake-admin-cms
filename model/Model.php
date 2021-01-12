<?php

namespace app\lakecms\model;

use think\Model as BaseModel;

use app\lakecms\support\Field as SupportField;

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
            ->sort(function($item) {
                return $item['sort'].$item['id'];
            })
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
                // 查看是否赋值
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
                    // 如果值是数组则转换成字符串，适用于复选框等类型
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
     * 表单验证的模型字段
     */
    public function validateFields()
    {
        $fields = $this->fields;
        
        $data = [];
        foreach ($fields as $field) {
            if (! empty($field['validate_rule'])) {
                $data['rule'][$field['name'] . '|' . $field['title']] = explode('|', $field['validate_rule']);
            }
            
            if (! empty($field['validate_message'])) {
                $validateMessages = lake_parse_attr($field['validate_message'] );
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
        $listGrids = SupportField::parseRule($this->validate_rule);
        return $listGrids;
    }
    
    /**
     * 检测提示
     */
    public function validateMessages()
    {
        $listGrids = SupportField::parseRule($this->validate_rule);
        return $listGrids;
    }

}
