<?php

namespace app\lakecms\controller;

/**
 * CMS首页
 *
 * @create 2021-1-17
 * @author deatil
 */
class Index extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        // SEO信息
        $this->setMetaTitle();
        $this->setMetaKeywords();
        $this->setMetaDescription();

        return $this->fetch('/index');
    }
}
