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
        
        $this->setViewPath();
    }

    /**
     * 插件模板目录
     */
    protected function setViewPath() 
    {        
        $viewPath = Template::themeViewPath();

        app('config')->set([
            'view_path' => $viewPath.'/',
        ], 'view');
    }    
}
