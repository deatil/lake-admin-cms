<?php

namespace app\lakecms\template;

use think\facade\Cache;

use Lake\TTree as Tree;

use app\lakecms\model\Navbar as NavbarModel;
use app\lakecms\model\Category as CategoryModel;
use app\lakecms\model\Tags as TagsModel;
use app\lakecms\model\Content as ContentModel;
use app\lakecms\model\Settings as SettingsModel;
use app\lakecms\model\ModelField as ModelFieldModel;

/**
 * 模版数据
 *
 * @create 2020-1-15
 * @author deatil
 */
class Model
{
    /**
     * 导航列表
     */
    public static function getNavbarList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($params['cache']) ? false : (int) $params['cache'];
        
        // 是否使用树结构
        $tree = isset($tag['tree']) ? true : false;
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = NavbarModel::field($field)
            ->where($map)
            ->where($condition)
            ->order($order)
            ->page($page, $limit)
            ->cache($cache)
            ->select()
            ->toArray();
        $total = NavbarModel::where($map)
            ->where($condition)
            ->count();
        
        if ($tree !== false) {
            $Tree = new Tree();
            $data = $Tree->withData($data)->buildArray(0);
        }
        
        return [$data, $total];
    }
    
    /**
     * 导航详情
     */
    public static function getNavbarInfo($tag = [])
    {
        if (! isset($tag['id'])) {
            return [];
        }
        
        $id = $tag['id'];
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = NavbarModel::field($field)
            ->where([
                'id' => $id,
            ])
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        return $info;
    }
    
    /**
     * 栏目列表
     */
    public static function getCateList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 缓存
        $cache = !isset($params['cache']) ? false : (int) $params['cache'];
        
        // 是否使用树结构
        $tree = isset($tag['tree']) ? true : false;
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = CategoryModel::with(['model'])
            ->field($field)
            ->where($map)
            ->where($condition)
            ->order($order)
            ->page($page, $limit)
            ->cache($cache)
            ->select()
            ->toArray();
        $total = CategoryModel::where($map)
            ->where($condition)
            ->count();
        
        if ($tree !== false) {
            $Tree = new Tree();
            $data = $Tree->withData($data)->buildArray(0);
        }
            
        return [$data, $total];
    }
    
    /**
     * 栏目详情
     */
    public static function getCateInfo($tag = [])
    {
        // 栏目ID
        $id = isset($tag['id']) ? $tag['id'] : '';
        
        // 栏目唯一标识
        $name = isset($tag['name']) ? $tag['name'] : '';
        
        // 栏目名称
        $title = isset($tag['title']) ? $tag['title'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($id) && empty($name) && empty($title)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = CategoryModel::field($field)
            ->where(function($query) use($id, $name, $title) {
                $query
                    ->whereOr([
                        'id' => $id,
                    ])
                    ->whereOr([
                        'name' => $name,
                    ])
                    ->whereOr([
                        'title' => $title,
                    ]);
            })
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        return $info;
    }
    
    /**
     * 栏目内容列表
     */
    public static function getCateContentList($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->where($condition)
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $cate = $cate->toArray();
        
        $map = [
            ['status', '=', 1],
        ];
        
        $query = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['categoryid', '=', $cate['id']],
            ])
            ->where($map);
        $query2 = clone $query;
        
        $data = $query->order("id DESC")
            ->field($field)
            ->page($page, $limit)
            ->select()
            ->toArray();
        $total = $query2->count();
        
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        foreach ($data as $key => $value) {
            $data[$key] = ContentModel::formatShowFields($modelField, $value);
        }
        
        return [$data, $total];
    }
    
    /**
     * 栏目内容详情
     */
    public static function getCateContentInfo($tag = [])
    {
        if (! isset($tag['id'])) {
            return [];
        }
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'id' => $id,
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where($map)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return $info;
    }
    
    /**
     * 栏目内容上一条
     */
    public static function getCateContentPrevInfo($tag = [])
    {
        if (! isset($tag['id'])) {
            return [];
        }
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '<', $id],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id DESC')
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return $info;
    }
    
    /**
     * 栏目内容下一条
     */
    public static function getCateContentNextInfo($tag = [])
    {
        if (! isset($tag['id'])) {
            return [];
        }
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '>', $id],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id ASC')
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return $info;
    }
    
    /**
     * 栏目单页详情
     */
    public static function getCatePageInfo($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->where(function($query) use($cateid, $catename) {
                $query
                    ->whereOr([
                        'id' => $cateid,
                    ])
                    ->whereOr([
                        'name' => $catename,
                    ]);
            })
            ->where([
                'type' => 2,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where($map)
            ->order('id ASC')
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select();
        $info = ContentModel::formatShowFields($modelField, $info);
        
        return $info;
    }
    
    /**
     * 标签列表
     */
    public static function getTagList($tag = [])
    {
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = TagsModel::where($map)
            ->where($condition)
            ->field($field)
            ->order($order)
            ->page($page, $limit)
            ->select()
            ->toArray();
        $total = TagsModel::where($map)
            ->where($condition)
            ->count();
            
        return [$data, $total];
    }
    
    /**
     * 标签详情
     */
    public static function getTagInfo($tag = [])
    {
        // 名称
        $name = isset($tag['name']) ? $tag['name'] : '';
        
        // 标题
        $title = isset($tag['title']) ? $tag['title'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($name) && empty($title)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = TagsModel::field($field)
            ->where(function($query) use($name, $title) {
                $query->whereOr([
                        'name' => $name,
                    ])
                    ->whereOr([
                        'title' => $title,
                    ]);
            })
            ->where($map)
            ->where($condition)
            ->find();
        if (empty($info)) {
            return [];
        }
        
        $info = $info->toArray();
        
        return $info;
    }
    
    /**
     * 设置
     */
    public static function getSetting($tag = [])
    {
        if (! isset($tag['name'])) {
            return '';
        }
        
        if (! isset($tag['default'])) {
            $tag['default'] = '';
        }
        
        return SettingsModel::config($tag['name'], $tag['default']);
    }

}
