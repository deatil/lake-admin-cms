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
        $cateid = $this->request->param('cateid');
        
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 6);
        $sort = $this->request->param('sort', 'asc');
        if (! in_array($sort, ['asc', 'desc'])) {
            $sort = 'asc';
        }
        
        // 内容
        $data = TemplateModel::getCateContentList([
            'cateid' => $cateid,
            'catename' => $catename,
            'page' => $page,
            'limit' => $limit,
            'order' => 'id ' . $sort,
            'inchildren' => 'inchildren',
        ]);
        if (empty($data)) {
            return $this->error(__('信息不存在'));
        }
        
        // 栏目
        $cate = Arr::only($data['cate'], [
            'id', 'name', 'title', 
            'keywords', 'description', 
            'cover', 'template_list'
        ]);
        if (empty($cate)) {
            return $this->error(__('信息不存在'));
        }
        
        $this->assign([
            'cate' => $cate,
            'list' => $data['list'],
            'total' => $data['total'],
        ]);
        
        // SEO信息
        $this->setMetaTitle($cate['title']);
        $this->setMetaKeywords($cate['keywords']);
        $this->setMetaDescription($cate['description']);
        
        // 模版
        $viewFile = Template::themeViewPath($cate['template_list']);
        
        return $this->fetch($viewFile);
    }
}
