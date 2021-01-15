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
         * close 是否闭合（0 或者1 默认1） 
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
        
        'navbarlist' => [
            'attr' => '', 
            'close' => 0,
        ],
        'contentlist' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 3,
        ],
        'content' => [
            'attr' => 'name', 
            'close' => 0,
        ],
        'page' => [
            'attr' => 'name', 
            'close' => 0,
        ],
        'prev' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 1,
        ],
        'next' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 1,
        ],
        'tags' => [
            'attr' => '', 
            'close' => 1, 
            'level' => 3,
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
        $parse .= '\think\facade\Db::execute(\'' . $sql . '\');';
        $parse .= ' ?>';
        return $parse;
    }
    
    /**
     * 信息列表
     */
    public function tagContentlist($tag, $content)
    {
        $id = isset($tag['id']) ? $tag['id'] : 'item';
        $empty = isset($tag['empty']) ? $tag['empty'] : '';
        $key = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod = isset($tag['mod']) ? $tag['mod'] : '2';
        
        $params = [];
        foreach ($tag as $k => & $v) {
            if (in_array($k, ['condition'])) {
                $v = $this->autoBuildVar($v);
            }
            $v = '"' . $v . '"';
            $params[] = '"' . $k . '"=>' . $v;
        }
        
        $var = md5(microtime());
        $parse = '<?php ';
        $parse .= '$__' . $var . '__ = \app\lakecms\template\Model::getContentList([' . implode(',', $params) . ']);';
        $parse .= ' ?>';
        $parse .= '{volist name="$__' . $var . '__" id="' . $id . '" empty="' . $empty . '" key="' . $key . '" mod="' . $mod . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        $parse .= '{php}$__NAVBARLIST__=$__' . $var . '__;{/php}';
        
        return $parse;
    }

}
