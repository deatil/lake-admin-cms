<?php

use think\helper\Arr;
use Lake\File;

use app\lakecms\model\Settings as SettingsModel;

// lcms配置信息
app()->config->set(lakecms_config(), 'lakecms');

/**
 * 获取配置
 */
function lakecms_config($key = null, $default = null) {
    $data = SettingsModel::getSettings();
    
    if (! empty($key)) {
        return Arr::get($data, $key, $default);
    }
    
    return $data;
}

/**
 * 获取配置
 */
function lakecms_template_path($path = '') {
    $templatePath = root_path('template');
    
    if (! file_exists($templatePath)) {
        mkdir($templatePath, 755);
    }
    
    return $templatePath . ($path ? $path . DIRECTORY_SEPARATOR : $path);
}

/**
 * 获取配置
 */
function lakecms_theme_path() {
    $theme = lakecms_config('web_theme', 'default');
    
    $path = lakecms_template_path($theme);
    
    return $path;
}

/**
 * 获取配置
 */
function lakecms_themes($path = null) {
    if (empty($path)) {
        $path = lakecms_template_path();
    }
    
    $themes = glob($path.'*');
    
    $newThemes = collect($themes)
        ->map(function($item) {
            $infoFile = $item . '/info.php';
            if (! empty($item) && file_exists($infoFile)) {
                $info = include $infoFile;
                
                $coverFile = $item . '/' . Arr::get($info, 'cover');
                if (file_exists($infoFile)) {
                    $coverData = file_get_contents($infoFile);
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
