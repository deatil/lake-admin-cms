<?php

namespace app\lakecms\controller;

/**
 * 搜索
 *
 * @create 2021-1-17
 * @author deatil
 */
class Search extends Base
{
    /**
     * 详情
     */
    public function index()
    {
        return $this->fetch('/index');
    }
}
