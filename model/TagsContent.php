<?php

namespace app\lakecms\model;

use think\Model;

/**
 * 标签关联内容
 */
class TagsContent extends Model
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_tags_content';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    public static function onBeforeInsert($model)
    {
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }

}
