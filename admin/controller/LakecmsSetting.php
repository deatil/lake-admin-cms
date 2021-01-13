<?php

namespace app\admin\controller;

use think\facade\Cache;

use Lake\File;

use app\lakecms\model\Settings as SettingsModel;

/**
 * 设置
 *
 * @create 2020-1-7
 * @author deatil
 */
class LakecmsSetting extends LakecmsBase
{
    /**
     * 设置
     */
    public function index()
    {
        // 默认配置
        $default_setting = [
            'web_site_status' => 0,
            'web_site_recycle' => '0',
            // 'web_theme' => 'lake',
    
            'web_site_logo' => '', // 网站LOGO
            'web_site_company' => '', // 公司名称
            'web_site_address' => '', // 地址
            'web_site_telphone' => '', // 电话
            'web_site_phone' => '', // 手机
            'web_site_email' => '', // 邮箱
            'web_site_icp' => '', // 备案信息
            'web_site_statistics' => '', // 站点代码
            
            'site_url' => '', // 站点代码
            'site_name' => '',
            'site_name' => '',
            'site_title' => '',
            'site_keyword' => '',
            'site_description' => '',
            'site_cache_time' => 3600,
        ];

        if ($this->request->isPost()) {
            $setting = $this->request->param('setting/a');
            $setting['web_site_status'] = isset($setting['web_site_status']) ? 1 : 0;
            
            $setting = array_merge($default_setting, $setting);

            if (!empty($setting)) {
                foreach ($setting as $key => $item) {
                    $info = SettingsModel::where([
                        'name' => $key,
                    ])->find();
                    
                    if (! empty($info)) {
                        SettingsModel::where([
                            'name' => $key,
                        ])->update([
                            'value' => $item,
                        ]);
                    } else {
                        SettingsModel::insert([
                            'name' => $key,
                            'value' => $item,
                        ]);
                    }
                }
            }
            
            Cache::delete("lakecms_setting");
            
            return $this->success('设置更新成功！');
        } else {
            $config = SettingsModel::column('name,value');
            
            $setting = [];
            if (!empty($config)) {
                foreach ($config as $val) {
                    $setting[$val['name']] = $val['value'];
                }
            }
            
            if (!empty($setting)) {
                $setting = array_merge($default_setting, $setting);
            } else {
                $setting = $default_setting;
            }
            
            $this->assign('setting', $setting);

            return $this->fetch();
        }

    }
    
    /**
     * 设置主题
     */
    public function theme()
    {
        if ($this->request->isPost()) {
            $name = $this->request->param('name', 'default');
            
            $info = SettingsModel::where([
                'name' => 'web_theme',
            ])->find();
            
            if (! empty($info)) {
                SettingsModel::where([
                    'name' => 'web_theme',
                ])->update([
                    'value' => $name,
                ]);
            } else {
                SettingsModel::insert([
                    'name' => 'web_theme',
                    'value' => $name,
                ]);
            }
            
            return $this->success('设置更新成功！');
        } else {
            $theme = SettingsModel::where([
                    'name' => 'web_theme',
                ])->value('value');
            $this->assign("theme", $theme);
            
            // 主题
            $themes = lakecms_themes();
            $this->assign("themes", $themes);

            return $this->fetch();
        }
    }
    
}
