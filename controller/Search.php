<?php

namespace app\lakecms\controller;

use app\lakecms\model\Category as CategoryModel;
use app\lakecms\model\Model as ModelModel;
use app\lakecms\model\ModelField as ModelFieldModel;
use app\lakecms\model\Content as ContentModel;

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
        // 分类
        $cateid = $this->request->param('cateid/d', 0);
        
        // 关键词
        $keywords = $this->request->param('keywords/s', '', 'trim,lake_safe_replace,strip_tags,htmlspecialchars');
        $keywords = str_replace('%', '', $keywords); 
        
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);
        
        // 时间范围
        $time = $this->request->param('time/s', '');
        
        // 关键字检测
        $result = $this->validate([
            'keywords' => $keywords,
        ], [
            'keywords|关键词' => 'require|chsDash|max:25',
        ]);
        if (true !== $result) {
            $this->error($result);
        }
        
        if ($time == 'day') {
            $search_time = time() - 86400;
            $sql_time = ' AND add_time > ' . $search_time;
        } elseif ($time == 'week') {
            $search_time = time() - 604800;
            $sql_time = ' AND add_time > ' . $search_time;
        } elseif ($time == 'month') {
            $search_time = time() - 2592000;
            $sql_time = ' AND add_time > ' . $search_time;
        } elseif ($time == 'year') {
            $search_time = time() - 31536000;
            $sql_time = ' AND add_time > ' . $search_time;
        } else {
            $search_time = 0;
            $sql_time = '';
        }

        $cate = CategoryModel::where([
            'id' => $cateid,
        ])->find();
        if (empty($cate)) {
            $this->error('选择分类错误');
        }
        
        $modelid = $cate['modelid'];
        
        $model = ModelModel::where([
            'id' => $modelid,
            'status' => 1,
        ])->find();
        if (empty($model)) {
            $this->error('分类错误');
        }
        
        $searchField = ModelFieldModel::where('modelid', $modelid)
            ->where('is_filter', 1)
            ->where('status', 1)
            ->column('name');
        if (empty($searchField)) {
            $this->error('没有设置搜索字段');
        }
        
        $where = '';
        foreach ($searchField as $vo) {
            $where .= "$vo like '%$keywords%' or ";
        }
        $where = '(' . substr($where, 0, -4) . ') ';
        $where .= " AND status='1' $sql_time";
        
        // 数据
        $data = ContentModel::newTable($model['tablename'])
            ->where([
                ['categoryid', '=', $cate['id']],
            ])
            ->where($where)
            ->order('add_time DESC')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 分页
        $page = $data->render();
        
        // 总数
        $total = $data->total();
        
        // 数据
        $data = $data->toArray();
        
        $this->assign([
            'data' => $data['data'],
            'page' => $page,
            'total' => $total,
        ]);
        
        // SEO信息
        $this->setMetaTitle($keywords . ' - 搜索');
        $this->setMetaKeywords($keywords);
        $this->setMetaDescription($keywords);

        return $this->fetch('/search');
    }
}
