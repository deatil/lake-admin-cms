<?php

namespace app\lakecms\model;

use think\Model as BaseModel;

/**
 * 内容
 */
class Content extends BaseModel
{
    // 设置当前模型对应的数据表名称
    protected $name = '';
    
    // 设置主键名
    protected $pk = '';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    /**
     * 模型链接
     */
    public static function newConnect($table, $pk = 'id')
    {
        $modelPrefix = 'lakecms_ext_';
        $prefix = app()->db->connect()->getConfig('prefix');
        $newTable = $prefix . $modelPrefix . $table;
        
        $model = new static();
        $model->name = $newTable;
        $model->pk = $pk;
        return $model;
    }
    
    public static function onBeforeInsert($model)
    {
        $model->setAttr('edit_time', time());
        $model->setAttr('edit_ip', request()->ip());
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }

}
