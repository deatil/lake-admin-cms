<?php

namespace app\lakecms\service;

use think\helper\Arr;

use app\lakecms\model\Settings as SettingsModel;

/**
 * 模版
 *
 * @create 2020-1-14
 * @author deatil
 */
class Template 
{
    /**
     * 模版根目录
     */
    public static function path($path = '') 
    {
        $templatePath = public_path('template');
        
        if (! file_exists($templatePath)) {
            mkdir($templatePath, 755);
        }
        
        return $templatePath . $path;
    }

    /**
     * 当前主题目录
     */
    public static function themePath($path = '') 
    {
        $theme = SettingsModel::config('web_theme', 'default');
        
        $themePath = static::path($theme);
        
        return $themePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * 当前模版目录
     */
    public static function themeViewPath($path = '') 
    {
        $themeViewPath = static::themePath('view');
        
        return $themeViewPath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * 所有主题
     */
    public static function themes($path = null) 
    {
        if (empty($path)) {
            $path = static::path();
        }
        
        $themes = glob($path.'/*');
        
        $newThemes = collect($themes)
            ->map(function($item) {
                $infoFile = $item . '/info.php';
                if (! empty($item) && file_exists($infoFile)) {
                    $info = include $infoFile;
                    
                    $coverFile = $item . '/' . Arr::get($info, 'cover');
                    if (file_exists($coverFile)) {
                        $coverData = file_get_contents(realpath($coverFile));
                        $cover = "data:image/png;base64,".base64_encode($coverData);
                    } else {
                        $cover = "";
                    }
                    
                    return [
                        'name' => Arr::get($info, 'name'),
                        'remark' => Arr::get($info, 'remark'),
                        'cover' => $cover,
                        'version' => Arr::get($info, 'version'),
                        'author' => Arr::get($info, 'author'),
                    ];
                }
                
                return null;
            })
            ->filter(function($item) {
                if (! empty($item)) {
                    return $item;
                }
                
                return null;
            })
            ->values()
            ->toArray();
        
        return $newThemes;
    }

}