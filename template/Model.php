<?php

namespace app\lakecms\template;

use think\facade\Cache;

use app\lakecms\model\Navbar as NavbarModel;
use app\lakecms\model\Content as ContentModel;

/**
 * 模版数据
 *
 * @create 2020-1-15
 * @author deatil
 */
class Model
{
    /**
     * 模型数据列表
     */
    public static function getContentList($tag = [])
    {
        if (! isset($tag['table'])) {
            return [];
        }
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 每页显示总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        $table = $tag['table'];
        
        $where = [];
        
        // 当前分类
        if (isset($tag['cateid'])) {
            $cateid = $tag['cateid'];
            $where[] = ['categoryid', '=', $cateid];
        }
        
        $list = ContentModel::newTable($table)
            ->where($where)
            ->order($order)
            ->paginate($limit);
        
        return $list;
    }
    
    /**
     * 导航数据列表
     */
    public static function getNavbarlist($tag = [])
    {
        // 缓存时间
        $cache = isset($tag['cache']) && intval($tag['cache']) ? intval($tag['cache']) : 0;
        
        // 排序
        $order = isset($tag['order']) ? $tag['order'] : "id DESC";
        
        // 当前页
        $page = isset($tag['page']) && intval($tag['page']) > 0 ? intval($tag['page']) : 0;
        
        // 每页显示总数
        $limit = isset($tag['limit']) && intval($tag['limit']) > 0 ? intval($tag['limit']) : 20;
        
        $where = [];
        $where[] = ['status', '=', 1];
        
        $name = md5("modellist-" . serialize($tag));
        $list = Cache::get($name);
        if (! $list) {
            $list = NavbarModel::where($where)
                ->order($order)
                ->page($page, $limit)
                ->select();
            if (empty($list)) {
                $list = [];
            } else {
                $list = $list->toArray();
            }
            
            Cache::set($name, $list, $cache);
        }
        
        return $list;
    }
}
