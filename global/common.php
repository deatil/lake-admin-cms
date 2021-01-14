<?php

use think\helper\Arr;
use Lake\File;

use app\lakecms\model\Settings as SettingsModel;
use app\lakecms\service\Template;

// lcms配置信息
app()->config->set(lakecms_config(), 'lakecms');

/**
 * 获取配置
 */
function lakecms_config($key = null, $default = null) {
    return SettingsModel::config($key, $default);
}

/**
 * 获取配置
 */
function lakecms_theme_path() {
    return Template::themePath();
}

/**
 * 获取配置
 */
function lakecms_themes($path = null) {
    return Template::themes($path);
}
