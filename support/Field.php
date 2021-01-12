<?php

namespace app\lakecms\support;

/**
 * 字段管理
 */
class Field 
{
    /**
     * 解析配置
     * @param string $data 配置值
     * @return array|string
     */
    public static function parseAttr($data = '')
    {
        $array = preg_split('/[,;\r\n ]+/', trim($data, ",;\r\n"));
        if (strpos($data, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        
        return $value;
    }
    
    /**
     * 解析规则
     * 
     * @param string $data 规则数据
     * @return array
     */
    public static function parseRule($data = '')
    {
        // $data = "key\r\nkey1"
        $array = preg_split('/[;\r\n ]+/', trim($data, ";\r\n"));
        return $array;
    }
    
    /**
     * 解析规则提示
     * 
     * @param string $data 规则提示
     * @return array
     */
    public static function parseMessage($data = '')
    {
        // $data = "key:value"
        $array = preg_split('/[,;\r\n ]+/', trim($data, ",;\r\n"));
        
        $value = [];
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val, 2);
            
            if (! empty($v)) {
                $value[$k] = $v;
            }
        }
        
        return $value;
    }
    
    /**
     * 解析表头
     * 
     * @param string $data 表头数据
     * @return array
     */
    public static function parseGrid($data = '')
    {
        // $data = "key:value|function"
        $array = preg_split('/[,;\r\n ]+/', trim($data, ",;\r\n"));
        
        if (! strpos($data, ':')) {
            return [];
        }
        
        $data = [];
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val, 2);
            
            $newV = explode('|', $v, 2);
            if (count($newV) == 2) {
                $title = $newV[0];
                $format = $newV[1];
            } else {
                $title = $v;
                $format = '';
            }
            
            $data[] = [
                'name' => $k,
                'title' => $title,
                'format' => $format,
            ];
        }
        
        return $data;
    }

}