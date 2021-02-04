<?php

namespace app\lakecms;

use Lake\File;
use Lake\Module;

use app\lakecms\model\Settings as SettingsModel;

/**
 * 安装脚本
 *
 * @create 2020-1-7
 * @author deatil
 */
class Install
{
    /**
     * 安装完回调
     * @return boolean
     */
    public function end()
    {    
        $Module = new Module();
        
        // 清除旧数据
        if (request()->param('clear') == 1) {
            // 
        }
        
        // 安装数据库
        $Module->runSQL(__DIR__ . "/install/install.sql");
        
        // 演示数据
        if (request()->param('demo') == 1) {
            $Module->runSQL(__DIR__ . "/install/demo.sql");
        }
        
        // 填充默认配置
        $setting = include __DIR__ . '/install/setting.php';
        if (! empty($setting) && is_array($setting)) {
            foreach ($setting as $key => $item) {
                SettingsModel::insert([
                    'name' => $key,
                    'value' => $item,
                ]);
            }
        }
        
        // 复制模版文件
        $fromPath = __DIR__ . DIRECTORY_SEPARATOR 
            . "install" . DIRECTORY_SEPARATOR
            . "template" . DIRECTORY_SEPARATOR;
        $toPath = root_path() 
            . 'public' . DIRECTORY_SEPARATOR 
            . 'template' . DIRECTORY_SEPARATOR;
        File::copyDir($fromPath, $toPath);
        
        return true;
    }

}
