<?php

use think\helper\Arr;
use Lake\File;

use app\lakecms\support\Pinyin;
use app\lakecms\service\Template;
use app\lakecms\model\Settings as SettingsModel;

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
function lakecms_theme_view_path() {
    return Template::themeViewPath();
}

/**
 * 获取配置
 */
function lakecms_themes($path = null) {
    return Template::themes($path);
}

/**
 * 获取中文字符拼音首字母组合
 */
function lakecms_get_py_first($zh) {
    return (new Pinyin())->getPinyinFirst($zh);
}

