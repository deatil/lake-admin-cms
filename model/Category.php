<?php

namespace app\lakecms\model;

use think\Model;

/**
 * 栏目
 */
class Category extends Model
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_category';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
}
