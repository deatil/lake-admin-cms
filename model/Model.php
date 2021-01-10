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

}