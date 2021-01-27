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
            
            if (isset($setting['web_site_logo'])) {
                $setting['web_site_logo'] = lake_get_file_path($setting['web_site_logo']);
            }
            
            Cache::set("lakecms_setting", $setting, 36000);
        }
        
        return $setting;
    }
    
    /**
     * 清除缓存
     */
    public static function clearCache()
    {
        return Cache::delete("lakecms_setting");
    }
    
    /**
     * 获取配置
     */
    public static function config($key = null, $default = null) 
    {
        $data = static::getSettings();
        
        return Arr::get($data, $key, $default);
    }
    
    /**
     * 网站logo
     */
    public static function logo() 
    {
        $logo = static::config('web_site_logo');
        
        return $logo;
    }

}
