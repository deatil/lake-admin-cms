<?php

namespace app\lakecms\controller;

/**
 * 标签
 *
 * @create 2021-1-17
 * @author deatil
 */
class Tag extends Base
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
    
    /**
     * 详情
     */
    public function detail()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', 0);
        $page = $this->request->param('page/d', 1);
        
        return $this->fetch('/index');
    }
}
