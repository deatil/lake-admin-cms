<?php

namespace app\lakecms\controller;

use Lake\Module\Controller\HomeBase;

use app\lakecms\service\Template;

/**
 * CMS
 *
 * @create 2021-1-17
 * @author deatil
 */
abstract class Base extends HomeBase
{    
    /**
     * 框架构造函数
     */
    protected function initialize()
    {
        parent::initialize();
        
        // seo相关信息
        $this->assign([
            'meta' => config('lakecms'),
        ]);
        
        // 重设模版目录
        $this->setViewPath();
    }

    /**
     * 设置标题
     */
    protected function setMetaTitle($title = '') 
    {
        if (! empty($title)) {
            $title = $title . ' - ' . config('lakecms.site_name');
        } else {
            $title = config('lakecms.site_name') . ' - ' . config('lakecms.site_slogan');
        }
        
        $this->assign('meta_title', $title);
    }

    /**
     * 设置关键字
     */
    protected function setMetaKeywords($keywords = '') 
    {
        if (empty($keywords)) {
            $keywords = config('lakecms.site_keywords');
        }
        
        $this->assign('meta_keywords', $keywords);
    }

    /**
     * 设置描述
     */
    protected function setMetaDescription($description = '') 
    {
        if (empty($description)) {
            $description = config('lakecms.site_description');
        }
        
        $this->assign('meta_description', $description);
    }

    /**
     * 模板目录
     */
    protected function setViewPath() 
    {        
        $viewPath = Template::themeViewPath();

        app('config')->set([
            'view_path' => $viewPath.'/',
        ], 'view');
        
        // 设置视图公用参数
        $this->assign([
            'lakecms_view_path' => $viewPath,
        ]);
    }
}
