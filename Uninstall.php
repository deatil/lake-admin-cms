<?php

namespace app\lakecms;

use think\facade\Db;

use Lake\Module;

use app\lakecms\service\Datatable;

/**
 * 卸载脚本
 *
 * @create 2020-1-7
 * @author deatil
 */
class Uninstall
{
    // 卸载
    public function run()
    {
        $Module = new Module();
        
        if (request()->param('clear') == 1) {
            // 
        }
        
        // 删除数据库
        $runSqlStatus = $Module->runSQL(__DIR__ . "/install/uninstall.sql");
        if (!$runSqlStatus) {
            return false;
        }

        return true;
    }

}
