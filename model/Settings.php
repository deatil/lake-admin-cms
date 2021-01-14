<?php

namespace app\lakecms\model;

use think\Model;
use think\facade\Cache;
use think\helper\Arr;

/**
 * 设置
 */
class Settings extends Model
{
    // 设置当前模型对应的数据表名称
    protected $name = 'lakecms_settings';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    /**
     * 获取配置
     */
    public static function getSettings()
    {
        $setting = Cache::get("lakecms_setting");
        
        if (! $setting) {
            $config = self::column('name,value');
            
            $setting = [];
            if (!empty($config)) {
                foreach ($config as $val) {
                    $setting[$val['name']] = $val['value'];
                }
            }
            
            Cache::set("lakecms_setting", $setting, 36000);
        }
        
        return $setting;
    }
    
    /**
     * 获取配置
     */
    public static function config($key = null, $default = null) 
    {
        $data = static::getSettings();
        
        if (! empty($key)) {
            return Arr::get($data, $key, $default);
        }
        
        return $data;
    }

}
