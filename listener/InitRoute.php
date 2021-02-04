<?php

namespace app\lakecms\listener;

use think\facade\Db;
use think\facade\Route;

/**
 * 初始化路由
 *
 * @create 2020-1-27
 * @author deatil
 */
class InitRoute
{
    /**
     * 设置路由
     */
    public function handle($params)
    {
        Route::rule('/', 'lakecms/index/index');
        Route::rule('tag/[:tag]', 'lcms/index/tags');
        Route::rule('search/<keyword?>/[:cateid]/[:time]', 'lcms/index/search')
            ->pattern([
                'keyword' => '\w+', 
                'cateid' => '\d+', 
                'time' => '\w+',
            ]);
    }

}
