<?php

namespace app\lakecms\controller;

use think\helper\Arr;

use app\lakecms\service\Template;
use app\lakecms\template\Model as TemplateModel;

/**
 * 单页
 *
 * @create 2021-1-30
 * @author deatil
 */
class Page extends Base
{
    /**
     * 详情
     *
     * eg:
     * /lakecms/page.html?catename=[name]
     */
    public function index()
    {
        // 栏目ID
        $cateid = $this->request->param('cateid/d', '');
        
        // 栏目标识
        $catename = $this->request->param('catename/s', '');
        
        // 内容
        $data = TemplateModel::getCatePageInfo([
            'cateid' => $cateid,
            'catename' => $catename,
            'viewinc' => 'views',
        ]);
        if (empty($data)) {
            return $this->error(__('信息不存在'));
        }
        
        // 分类
        $cate = $data['cate'];
        
        // 内容
        $info = $data['info'];
        
        $this->assign([
            'cate' => $cate,
            'info' => $info,
        ]);
        if (empty($info)) {
            return $this->error(__('信息不存在'));
        }
        
        // SEO信息
        $this->setMetaTitle($info['title']);
        $this->setMetaKeywords($info['keywords']);
        $this->setMetaDescription($info['description']);
        
        // 模版
        $viewFile = Template::themeViewPath($cate['template_page']);
        
        return $this->fetch($viewFile);
    }
}
