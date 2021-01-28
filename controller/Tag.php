<?php

namespace app\lakecms\controller;

use app\lakecms\template\Model as TemplateModel;

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
        // 页码
        $page = $this->request->param('page/d', 1);
        
        // 排序
        $sort = $this->request->param('sort', 'hot');
        if ($sort == 'time') {
            $order = 'add_time DESC, id DESC';
        } elseif ($sort == 'hot') {
            $order = 'views DESC, id DESC';
        } else {
            $order = 'add_time DESC, id DESC';
        }
        
        $limit = 20;
        
        // 内容
        $data = TemplateModel::getTagList([
            'page' => $page,
            'limit' => $limit,
            'order' => $order,
        ]);
        
        $this->assign([
            'list' => $data['list'],
            'total' => $data['total'],
            'page' => $data['page'],
        ]);
        
        // SEO信息
        $this->setMetaTitle('标签');
        $this->setMetaKeywords('标签,标签列表');
        $this->setMetaDescription('标签');
        
        return $this->fetch('/tag');
    }
    
    /**
     * 详情
     */
    public function detail()
    {
        // 名称
        $title = $this->request->param('title/s');
        
        // 内容
        $data = TemplateModel::getTagInfo([
            'title' => $title,
            'viewinc' => 1,
        ]);
        if (empty($data)) {
            return $this->error($title . '标签不存在');
        }
        
        $this->assign([
            'data' => $data
        ]);
        
        // SEO信息
        $this->setMetaTitle($data['title'] . ' - 标签');
        $this->setMetaKeywords($data['keywords']);
        $this->setMetaDescription($data['description']);
        
        return $this->fetch('/tag_detail');
    }
}
