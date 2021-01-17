<?php

namespace app\lakecms\controller;

use think\facade\Db;

use app\lakecms\model\Category as CategoryModel;

/**
 * 分类
 *
 * @create 2021-1-17
 * @author deatil
 */
class Cate extends Base
{
    /**
     * 列表
     */
    public function index()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', 0);
        $page = $this->request->param('page/d', 1);
        
        return $this->fetch('/index');
    }
}
