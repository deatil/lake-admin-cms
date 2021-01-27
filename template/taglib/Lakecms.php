<?php

namespace app\lakecms\template\taglib;

use think\template\TagLib;

use app\lakecms\service\Template;
use app\lakecms\template\Model;

/**
 * 模版标签
 *
 * @create 2020-1-15
 * @author deatil
 */
class Lakecms extends Taglib
{
    // 标签定义
    protected $tags = [
        /**
         * 标签定义： 
         * attr 属性列表 
         * close 是否闭合（0 或者 1，默认 1） 
         * alias 标签别名 
         * level 嵌套层次
         */
        'template' => [
            'attr' => 'file', 
            'close' => 0,
        ],
        
        'execute' => [
            'attr' => 'sql', 
            'close' => 0,
        ],
        
        'navbars' => [
            'attr' => 'return,empty,key,mod,paginate,pagetotal,page,limit,order,field,condition,cache,tree', 
            'close' => 1,
            'level' => 3,
        ],
        'navbar' => [
            'attr' => 'return,navbarid,field,condition', 
            'attr' => '', 
            'close' => 1,
        ],
        
        'cates' => [
            'attr' => 'return,empty,key,mod,paginate,pagetotal,page,limit,order,field,condition,cache,tree', 
            'close' => 1,
            'level' => 3,
        ],
        'cate' => [
            'attr' => 'return,cateid,field,condition', 
            'close' => 1,
        ],
        
        'contents' => [
            'attr' => 'cateid,catename,return,empty,key,mod,paginate,pagetotal,page,limit,order,field,condition,cache,tree', 
            'close' => 1, 
            'level' => 3,
        ],
        'content' => [
            'attr' => 'return,cateid,catename,contentid,field,condition', 
            'close' => 1,
        ],
        'contentprev' => [
            'attr' => 'return,cateid,catename,contentid,field,condition', 
            'close' => 1, 
            'level' => 1,
        ],
        'contentnext' => [
            'attr' => 'return,cateid,catename,contentid,field,condition', 
            'close' => 1, 
            'level' => 1,
        ],
        
        'page' => [
            'attr' => 'return,cateid,catename,field,condition', 
            'close' => 1,
        ],
        
        'tags' => [
            'attr' => 'return,empty,key,mod,paginate,pagetotal,page,limit,order,field,condition,cache,tree', 
            'close' => 1, 
            'level' => 3,
        ],
        'tag' => [
            'attr' => 'return,name,title,field,condition', 
            'close' => 1, 
        ],
        
        'setting' => [
            'attr' => 'name,default', 
            'close' => 1, 
        ],
    ];
    
    /**
     * 加载模版
     */
    public function tagTemplate($tag, $content)
    {
        $templateFile = $tag['file'];
        
        if (! file_exists($templateFile)) {
            $viewSuffix = config('view.view_suffix');
            $templateFile = Template::themePath($templateFile . '.' . $viewSuffix);
            
            if (! file_exists($templateFile)) {
                return '';
            }
        }
        
        // 读取内容
        $tmplContent = file_get_contents($templateFile);
        
        // 解析模板
        $this->tpl->parse($tmplContent);
        return $tmplContent;
    }
    
    /**
     * 执行sql
     */
    public function tagExecute($tag, $content)
    {
        $sql = isset($tag['sql']) ? $tag['sql'] : '';
        $sql = addslashes($sql);
        $parse = '<?php ';
        $parse .= 'echo \think\facade\Db::execute(\'' . $sql . '\');';
        $parse .= ' ?>';
        return $parse;
    }
    
    /**
     * 导航列表
     */
    public function tagNavbars($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        
        // 分页
        $paginate = isset($tag['paginate']) ? $tag['paginate'] : 'paginate';
        $pagetotal = isset($tag['pagetotal']) ? $tag['pagetotal'] : 'pagetotal';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime().mt_rand(10000, 99999));
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getNavbarList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{php}$__LAKECMS_NAVBARS_LIST__ = $__' . $var . '_list__ = $__' . $var . '__["list"];{/php}';
        $parse .= '{php}$__LAKECMS_NAVBARS_PAGE__ = $paginate = $__' . $var . '__["page"];{/php}';
        $parse .= '{php}$__LAKECMS_NAVBARS_TOTAL__ = $pagetotal = $__' . $var . '__["total"];{/php}';
        $parse .= '{volist name="$__' . $var . '_list__" id="' . $return . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 导航详情
     */
    public function tagNavbar($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'navbar';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php';
        $parse .= '$' . $return . ' = \app\lakecms\template\Model::getNavbarInfo([' . implode(',', $params) . ']);';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif; ?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 分类列表
     */
    public function tagCates($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        
        // 分页
        $paginate = isset($tag['paginate']) ? $tag['paginate'] : 'paginate';
        $pagetotal = isset($tag['pagetotal']) ? $tag['pagetotal'] : 'pagetotal';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime().mt_rand(10000, 99999));
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getCateList([' . implode(',', $params) . ']);';
        $parse .= '$__' . $var . '_list__ = ' . '$__' . $var . '__["list"];';
        $parse .= '$__' . $var . '_total__ = ' . '$__' . $var . '__["total"];';
        $parse .= '$__' . $var . '_page__ = ' . '$__' . $var . '__["page"];';
        $parse .= ' ?>';
        $parse .= '{php}$__LAKECMS_CATES_LIST__ = $__' . $var . '_list__;{/php}';
        $parse .= '{php}$__LAKECMS_CATES_PAGE__ = $paginate = $__' . $var . '_page__;{/php}';
        $parse .= '{php}$__LAKECMS_CATES_TOTAL__ = $pagetotal = $__' . $var . '_total__;{/php}';
        $parse .= '{volist name="$__' . $var . '_list__" id="' . $return . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 分类详情
     */
    public function tagCate($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'cate';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php';
        $parse .= '$' . $return . ' = \app\lakecms\template\Model::getCateInfo([' . implode(',', $params) . ']);';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif; ?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 信息列表
     */
    public function tagContents($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        
        // 分页
        $paginate = isset($tag['paginate']) ? $tag['paginate'] : 'paginate';
        $pagetotal = isset($tag['pagetotal']) ? $tag['pagetotal'] : 'pagetotal';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime().mt_rand(10000, 99999));
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getCateContentList([' . implode(',', $params) . ']);';
        $parse .= '$__' . $var . '_cate__ = ' . '$__' . $var . '__["cate"];';
        $parse .= '$__' . $var . '_list__ = ' . '$__' . $var . '__["list"];';
        $parse .= '$__' . $var . '_total__ = ' . '$__' . $var . '__["total"];';
        $parse .= '$__' . $var . '_page__ = ' . '$__' . $var . '__["page"];';
        $parse .= ' ?>';
        $parse .= '{php}$__LAKECMS_CONTENTS_CATE__ = $pagetotal = $__' . $var . '_cate__;{/php}';
        $parse .= '{php}$__LAKECMS_CONTENTS_TOTAL__ = $pagetotal = $__' . $var . '_total__;{/php}';
        $parse .= '{php}$__LAKECMS_CONTENTS_LIST__ = $__' . $var . '_list__;{/php}';
        $parse .= '{php}$__LAKECMS_CONTENTS_PAGE__ = $paginate = $__' . $var . '_page__;{/php}';
        $parse .= '{volist name="$__' . $var . '_list__" id="' . $return . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 信息详情
     */
    public function tagContent($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'content';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime().mt_rand(10000, 99999));
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getCateContentInfo([' . implode(',', $params) . ']);';
        $parse .= '$' . $return . ' = ' . '$__' . $var . '__["info"];';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif;?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 信息上一条
     */
    public function tagContentprev($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'contentprev';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getCateContentPrevInfo([' . implode(',', $params) . ']);';
        $parse .= '$' . $return . ' = ' . '$__' . $var . '__["info"];';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif;?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 信息下一条
     */
    public function tagContentnext($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'contentnext';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getCateContentNextInfo([' . implode(',', $params) . ']);';
        $parse .= '$' . $return . ' = ' . '$__' . $var . '__["info"];';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif;?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 单页
     */
    public function tagPage($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'contentnext';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php ';
        $parse .= '$' . $return . ' = \app\lakecms\template\Model::getCatePageInfo([' . implode(',', $params) . ']);';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif; ?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 标签列表
     */
    public function tagTags($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        
        // 分页
        $paginate = isset($tag['paginate']) ? $tag['paginate'] : 'paginate';
        $pagetotal = isset($tag['pagetotal']) ? $tag['pagetotal'] : 'pagetotal';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime().mt_rand(10000, 99999));
        $parse = '<?php ';
        $parse .= 'list($__' . $var . '_list__, $__' . $var . '_total__, $__' . $var . '_page__) = \app\lakecms\template\Model::getTagList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{php}$__LAKECMS_TAGS_LIST__ = $__' . $var . '_list__;{/php}';
        $parse .= '{php}$__LAKECMS_TAGS_PAGE__ = $paginate = $__' . $var . '_page__;{/php}';
        $parse .= '{php}$__LAKECMS_TAGS_TOTAL__ = $pagetotal = $__' . $var . '_total__;{/php}';
        $parse .= '{volist name="$__' . $var . '_list__" id="' . $return . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 标签详情
     */
    public function tagTag($tag, $content)
    {
        $return = isset($tag['return']) ? $tag['return'] : 'tag';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $parse = '<?php';
        $parse .= '$' . $return . ' = \app\lakecms\template\Model::getTagInfo([' . implode(',', $params) . ']);';
        $parse .= 'if ($' . $return . '):';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php endif; ?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 设置项
     */
    public function tagSetting($tag, $content)
    {
        $name = isset($tag['name']) ? $tag['name'] : '';
        if (empty($name)) {
            return null;
        }
        
        $default = isset($tag['default']) ? $tag['default'] : '';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(json_encode($params));
        $parse = '<?php';
        $parse .= 'if (! $__' . $var . '__) { ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getSetting([' . implode(',', $params) . ']);';
        $parse .= ' } ?>';
        $parse .= '<?php echo $__' . $var . '__; ?>';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }

}
