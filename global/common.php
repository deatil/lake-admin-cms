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
    
    $newThemes = collect($themes)->map(function ($item) use($path) {
        return substr($item, strlen($path));
    });
    
    return $newThemes;
}
