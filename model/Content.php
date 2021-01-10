<?php

namespace app\lakecms\model;

use think\facade\Db;

/**
 * 内容
 */
class Content
{
    /**
     * 模型链接
     */
    public static function name($table)
    {
        $modelPrefix = 'lakecms_ext_';
        $newTable = $modelPrefix . $table;
        
        $model = Db::name($newTable);
        return $model;
    }

}
