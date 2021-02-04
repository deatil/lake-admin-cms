<?php

namespace app\lakecms\listener;

use think\facade\Env;
use think\facade\View;

/**
 * 初始化模板
 *
 * @create 2020-1-27
 * @author deatil
 */
class InitTemplate
{
    /**
     * 设置信息
     */
    public function handle()
    {
        $viewPath = Template::themeViewPath();
        
        // 模块模板路径
        Env::set([
            'lakecms_view_path' => $viewPath,
        ]);
        
        // 设置视图公用参数
        View::assign([
            'lakecms_view_path' => $viewPath,
        ]);
        
    }

}
