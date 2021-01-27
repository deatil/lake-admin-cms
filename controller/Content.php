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
        // 栏目标识
        $catename = $this->request->param('catename/d', 1);
        
        // 内容ID
        $contentid = $this->request->param('id/d', 0);
        
        // 内容
        $data = TemplateModel::getCateContentInfo([
            'catename' => $catename,
            'contentid' => $contentid,
        ]);
        
        // 栏目
        $cate = Arr::only($data['cate'], [
            'id', 'name', 'title', 
            'keywords', 'description', 
            'cover', 'template_detail'
        ]);
        
        // 内容
        $info = $data['info'];
        
        $this->assign([
            'cate' => $cate,
            'info' => $info,
        ]);
        
        // SEO信息
        $this->setMetaTitle($info['title'] . ' - ' . $cate['title']);
        $this->setMetaKeywords($info['keywords']);
        $this->setMetaDescription($info['description']);
        
        return $this->fetch($cate['template_list']);
    }
}
