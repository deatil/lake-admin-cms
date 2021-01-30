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
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        if ($tree !== false) {
            $Tree = new Tree();
            $list = $Tree->withData($dataList['data'])->buildArray(0);
        } else {
            $list = $list['data'];
        }
        
        // 总数
        $total = $data->total();
        
        // 分页
        $page = $data->render();
        
        return [
            'list' => $list, 
            'total' => $total,
            'page' => $page,
        ];
    }
    
    /**
     * 导航详情
     */
    public static function getNavbarInfo($tag = [])
    {
        if (! isset($tag['navbarid'])) {
            return [];
        }
        
        $id = $tag['navbarid'];
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
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
        
        // 添加阅读量
        if (! empty($viewinc)) {
            NavbarModel::where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
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
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        if ($tree !== false) {
            $Tree = new Tree();
            $list = $Tree->withData($dataList['data'])->buildArray(0);
        } else {
            $list = $list['data'];
        }
        
        // 总数
        $total = $data->total();
        
        // 分页
        $page = $data->render();
        
        return [
            'list' => $list, 
            'total' => $total,
            'page' => $page,
        ];
    }
    
    /**
     * 栏目详情
     */
    public static function getCateInfo($tag = [])
    {
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $name = isset($tag['name']) ? $tag['name'] : '';
        
        // 栏目名称
        $title = isset($tag['title']) ? $tag['title'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($id) && empty($name) && empty($title)) {
            return [];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = CategoryModel::field($field)
            ->where(function($query) use($cateid, $name, $title) {
                $query
                    ->whereOr([
                        'id' => $cateid,
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
        
        // 添加阅读量
        if (! empty($viewinc)) {
            CategoryModel::where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
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
            return [
                'cate' => [], 
                'list' => [], 
                'total' => 0,
                'page' => '',
            ];
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
        
        // 缓存
        $cache = !isset($params['cache']) ? false : (int) $params['cache'];
        
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
            return [
                'cate' => [], 
                'list' => [], 
                'total' => 0,
                'page' => '',
            ];
        }
        
        $cate = $cate->toArray();
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['categoryid', '=', $cate['id']],
            ])
            ->where($map)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        $list = $dataList['data'];
        
        // 总数
        $total = $data->total();
        
        // 分页
        $page = $data->render();
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        foreach ($list as $key => $value) {
            $list[$key] = ContentModel::formatShowFields($modelField, $value);
        }
        
        return [
            'cate' => $cate, 
            'list' => $list, 
            'total' => $total,
            'page' => $page,
        ];
    }
    
    /**
     * 栏目内容详情
     */
    public static function getCateContentInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
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
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $cate = $cate->toArray();
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'id' => $contentid,
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
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
        
        $viewField = '';
        foreach ($modelField as $field) {
            if ($field['is_view'] == 1) {
                $viewField = $field['name'];
                break;
            }
        }
        
        // 添加阅读量
        if (! empty($viewinc) && ! empty($viewField)) {
            ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewField, 1)
                ->update();
        }
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 栏目内容上一条
     */
    public static function getCateContentPrevInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
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
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '<', $contentid],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id DESC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
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
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 栏目内容下一条
     */
    public static function getCateContentNextInfo($tag = [])
    {
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 栏目ID
        $cateid = isset($tag['cateid']) ? $tag['cateid'] : '';
        
        // 栏目唯一标识
        $catename = isset($tag['catename']) ? $tag['catename'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        if (empty($contentid) || (empty($cateid) && empty($catename))) {
            return [
                'cate' => [], 
                'info' => [], 
            ];
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
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                ['id', '>', $contentid],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->where($map)
            ->order('id ASC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
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
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
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
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
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
            return [
                'cate' => [], 
                'info' => [], 
            ];
        }
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->field($field)
            ->where([
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
            ->order('id DESC')
            ->find();
        if (empty($info)) {
            return [
                'cate' => $cate, 
                'info' => [], 
            ];
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
        
        // 添加阅读量
        if (! empty($viewinc)) {
            ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
        return [
            'cate' => $cate, 
            'info' => $info, 
        ];
    }
    
    /**
     * 模型内容列表
     */
    public static function getModelContentList($tag = [])
    {
        // 模型表
        $table = isset($tag['table']) ? $tag['table'] : '';
        
        if (empty($table)) {
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
        
        // 缓存
        $cache = !isset($params['cache']) ? false : (int) $params['cache'];
        
        $data = ContentModel::newTable($table)
            ->field($field)
            ->where([
                ['status', '=', 1],
            ])
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 格式化数据
        $modelField = ModelFieldModel::where([
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        foreach ($data as $key => $value) {
            $data->{$key} = ContentModel::formatShowFields($modelField, $value);
        }
        
        return $data;
    }
    
    /**
     * 模型内容详情
     */
    public static function getModelContentInfo($tag = [])
    {
        // 模型表
        $table = isset($tag['table']) ? $tag['table'] : '';
        
        // 内容ID
        $contentid = isset($tag['contentid']) ? $tag['contentid'] : '';
        
        // 查询字段
        $field = empty($tag['field']) ? '*' : $tag['field'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        // 阅读量
        $viewinc = isset($tag['viewinc']) ? $tag['viewinc'] : '';
        
        if (empty($contentid) || empty($table)) {
            return [];
        }
        
        $info = ContentModel::newTable($table)
            ->field($field)
            ->where([
                'id' => $contentid,
            ])
            ->where($condition)
            ->where([
                ['status', '=', 1],
            ])
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
        
        // 添加阅读量
        if (! empty($viewinc)) {
            ContentModel::newTable($table)
                ->where([
                    'id' => $info['id'],
                ])
                ->inc($viewinc, 1)
                ->update();
        }
        
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
        
        // 缓存
        $cache = !isset($params['cache']) ? false : (int) $params['cache'];
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = TagsModel::where($map)
            ->where($condition)
            ->field($field)
            ->order($order)
            ->cache($cache)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        
        // 列表
        $dataList = $data->toArray();
        $list = $dataList['data'];
        
        // 总数
        $total = $data->total();
        
        // 分页
        $page = $data->render();
        
        return [
            'list' => $list, 
            'total' => $total,
            'page' => $page,
        ];
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
        
        // 阅读量字段，为空不启用
        $viewinc = isset($tag['viewinc']) ? intval($tag['viewinc']) : '';
        
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
        
        // 添加阅读量
        if (! empty($viewinc)) {
            TagsModel::where($map)
                ->whereOr([
                    'title' => $title,
                ])
                ->whereOr([
                    'name' => $name,
                ])
                ->inc('views', 1)
                ->update();
        }
        
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
