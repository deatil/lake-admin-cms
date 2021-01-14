<?php

namespace app\lakecms;

use think\facade\Db;

use Lake\Module;

use app\lakecms\service\Model as ModelService;
use app\lakecms\model\Model as ModelModel;

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
            // 删除模型表
            $models = ModelModel::order('id ASC')->select();
            foreach ($models as $model) {
                ModelModel::where([
                    'id' => $model['id'],
                ])->delete();
                
                // 删除表
                ModelService::create()->deleteTable($model['tablename']);
            }
            
            // 删除数据库
            $runSqlStatus = $Module->runSQL(__DIR__ . "/install/uninstall.sql");
            if (!$runSqlStatus) {
                return false;
            }
        }

        return true;
    }

}
