<?php

namespace app\lakecms\controller;

use think\facade\Db;

use app\lakecms\model\Category as CategoryModel;

/**
 * 内容详情
 *
 * @create 2021-1-17
 * @author deatil
 */
class Content extends Base
{
    /**
     * 详情
     */
    public function index()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', 0);
        $page = $this->request->param('page/d', 1);
        
        return $this->fetch('/index');
    }
}
