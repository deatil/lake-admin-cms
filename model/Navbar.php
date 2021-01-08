<?php

namespace app\lakecms\model;

use think\Model;

/**
 * 导航
 */
class Navbar extends Model
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_navbar';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
}
