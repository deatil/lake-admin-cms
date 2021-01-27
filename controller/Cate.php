<?php

namespace app\lakecms\controller;

use think\helper\Arr;

use app\lakecms\service\Template;
use app\lakecms\template\Model as TemplateModel;

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
        // 栏目唯一标识
        $catename = $this->request->param('catename');
        
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        $sort = $this->request->param('sort', 'asc');
        if (! in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';
        }
        
        // 内容
        $data = TemplateModel::getCateContentList([
            'catename' => $catename,
            'page' => $page,
            'limit' => $limit,
            'order' => 'id ' . $sort,
        ]);
        
        // 栏目
        $cate = Arr::only($data['cate'], [
            'id', 'name', 'title', 
            'keywords', 'description', 
            'cover', 'template_list'
        ]);
        
        $this->assign([
            'cate' => $cate,
            'list' => $data['list'],
            'total' => $data['total'],
            'page' => $data['page'],
        ]);
        
        // SEO信息
        $this->setMetaTitle($cate['title']);
        $this->setMetaKeywords($cate['keywords']);
        $this->setMetaDescription($cate['description']);
        
        return $this->fetch($cate['template_list']);
    }
}
