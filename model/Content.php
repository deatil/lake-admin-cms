<?php

namespace app\lakecms\model;

use think\Model as BaseModel;

/**
 * 内容
 */
class Content extends BaseModel
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_ext_';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    // 清除默认设置数据，防止报错
    protected static $maker = [];
    
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
     * 新建模型表
     */
    public static function newTable($table)
    {
        return self::suffix($table);
    }
    
    /**
     * 新建数据
     */
    public static function newCreate(
        string $table, 
        array $data, 
        array $allowField = [], 
        bool $replace = false
    ) {
        return self::create($data, $allowField, $replace, $table);
    }
    
    /**
     * 更新数据
     */
    public static function newUpdate(
        string $table, 
        array $data, 
        $where = [], 
        array $allowField = []
    ) {
        return self::update($data, $where, $allowField, $table);
    }

}
