<?php

namespace app\lakecms\template;

use think\facade\Cache;

use app\lakecms\model\Navbar as NavbarModel;
use app\lakecms\model\Category as CategoryModel;
use app\lakecms\model\Tags as TagsModel;
use app\lakecms\model\Content as ContentModel;
use app\lakecms\model\Settings as SettingsModel;

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
        
        $field = empty($params['field']) ? '*' : $params['field'];
        $cache = !isset($params['cache']) ? $config['cachelifetime'] === 'true' ? true : (int)$config['cachelifetime'] : (int)$params['cache'];
        $cache = !$cache ? false : $cache;
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = NavbarModel::with(['model'])
            ->field($field)
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
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = NavbarModel::where([
                'id' => $id,
            ])
            ->where($map)
            ->where($condition)
            ->find();
        
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
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = CategoryModel::with(['model'])
            ->where($map)
            ->where($condition)
            ->order($order)
            ->page($page, $limit)
            ->select()
            ->toArray();
        $total = CategoryModel::where($map)
            ->where($condition)
            ->count();
            
        return [$data, $total];
    }
    
    /**
     * 栏目详情
     */
    public static function getCateInfo($tag = [])
    {
        if (! isset($tag['id'])) {
            return [];
        }
        
        $id = $tag['id'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = CategoryModel::where([
                'id' => $id,
            ])
            ->where($map)
            ->where($condition)
            ->find();
        
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
        $catename = isset($tag['catename']) ? $tag['cateid'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 1;
        
        // 每页总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $cate = CategoryModel::with(['model'])
            ->orWhere([
                'id' => $cateid,
            ])
            ->orWhere([
                'name' => $catename,
            ])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->where($condition)
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $modelField = ModelFieldModel::where([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        
        $query = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['categoryid', '=', $cate['id']],
            ])
            ->where($map);
        $data = $query->order("id DESC")
            ->page($page, $limit)
            ->select()
            ->toArray();
        $total = $query->count();
        
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
        $catename = isset($tag['catename']) ? $tag['cateid'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->orWhere([
                'id' => $cateid,
            ])
            ->orWhere([
                'name' => $catename,
            ])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                'id' => $id,
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->find();
        
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
        $catename = isset($tag['catename']) ? $tag['cateid'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->orWhere([
                'id' => $cateid,
            ])
            ->orWhere([
                'name' => $catename,
            ])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', '<', $id],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->order('id DESC')
            ->find();
        
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
        $catename = isset($tag['catename']) ? $tag['cateid'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->orWhere([
                'id' => $cateid,
            ])
            ->orWhere([
                'name' => $catename,
            ])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        $id = $tag['id'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', '>', $id],
                ['categoryid', '=', $cate['id']],
            ])
            ->where($condition)
            ->order('id ASC')
            ->find();
        
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
        $catename = isset($tag['catename']) ? $tag['cateid'] : '';
        
        if (empty($cateid) && empty($catename)) {
            return [];
        }
        
        $cate = CategoryModel::with(['model'])
            ->orWhere([
                'id' => $cateid,
            ])
            ->orWhere([
                'name' => $catename,
            ])
            ->where([
                'type' => 2,
                'status' => 1,
            ])
            ->find();
        if (empty($cate)) {
            return [];
        }
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $info = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                'categoryid' => $cate['id'],
            ])
            ->where($condition)
            ->order('id ASC')
            ->find();
        if (empty($info)) {
            return [];
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
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $data = TagsModel::with(['model'])
            ->where($map)
            ->where($condition)
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
        if (! isset($tag['name'])) {
            return [];
        }
        
        $name = $tag['name'];
        
        // 附加条件
        $condition = isset($tag['condition']) ? $tag['condition'] : '';
        
        $map = [
            ['status', '=', 1],
        ];
        
        $info = TagsModel::where([
                'name' => $name,
            ])
            ->where($map)
            ->where($condition)
            ->find();
        
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
