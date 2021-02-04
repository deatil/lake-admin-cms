<?php

use think\helper\Arr;
use Lake\File;

use app\lakecms\support\Pinyin;
use app\lakecms\service\Template;
use app\lakecms\model\Settings as SettingsModel;

// 配置信息
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
    return Pinyin::encode($zh, 'all', 'utf8');
}

/**
 * 栏目链接
 */
function lakecms_cate_url($catename) {
    if (is_int($catename)) {
        $url = url('lakecms/cate/index', [
            'cateid' => $catename, 
            'page' => '[PAGE]',
        ]);
    } else {
        $url = url('lakecms/cate/index', [
            'catename' => $catename, 
            'page' => '[PAGE]',
        ]);
    }
    
    return $url;
}

/**
 * 详情链接
 */
function lakecms_content_url($catename, $contentid) {
    if (is_int($catename)) {
        $url = url('lakecms/content/index', [
            'cateid' => $catename, 
            'id' => $contentid,
        ]);
    } else {
        $url = url('lakecms/content/index', [
            'catename' => $catename, 
            'id' => $contentid,
        ]);
    }
    
    return $url;
}

/**
 * 单页链接
 */
function lakecms_page_url($catename) {
    if (is_int($catename)) {
        $url = url('lakecms/page/index', [
            'cateid' => $catename, 
        ]);
    } else {
        $url = url('lakecms/page/index', [
            'catename' => $catename, 
        ]);
    }
    
    return $url;
}

