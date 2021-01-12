<?php

namespace app\lakecms\model;

use think\Model as BaseModel;

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
    public function formFields($showType = 1)
    {
        $fields = $this->fields;
        
        $data = collect($fields)
            ->map(function($item) use($showType) {
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
                    'remark' => $item['remark'],
                    'ifrequire' => $item['is_must'],
                    'fieldArr' => $this->tablename,
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

}
