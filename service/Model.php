<?php

namespace app\lakecms\service;

use think\facade\Db;

use app\lakecms\support\Datatable;

/**
 * 模型
 *
 * @create 2020-1-8
 * @author deatil
 */
class Model 
{
    /**
     * 创建
     */
    public static function create()
    {
        return new static();
    }
    
    /**
     * datatable
     */
    public function getDatatable()
    {
        $modelPrefix = 'lakecms_ext_';
        $prefix = app()->db->connect()->getConfig('prefix');
        $datatable = new Datatable($modelPrefix, $prefix);
        $datatable->setCharset('utf8mb4')
            ->setEngineType('MyISAM');
        
        return $datatable;
    }
    
    /**
     * 创建表
     */
    public function createTable(
        $table = '', 
        $comment = '', 
        $pk = 'id', 
        $charset = null, 
        $engine_type = null
    ) {
        $datatable = $this->getDatatable();
        if ($datatable->checkTable($table)) {
            return false;
        }
        
        $result = $datatable
            ->createTable($table, $comment, $pk, $charset, $engine_type)
            ->query();
            
        return $result;
    }
    
    /**
     * 更新数据表
     */
    public function updateTableName($oldTable = '', $newTable = '') 
    {
        $result = $this->getDatatable()
            ->updateTableName($oldTable, $newTable)
            ->query();
            
        return $result;
    }
    
    /**
     * 删除数据表
     */
    public function deleteTable($table = '') 
    {
        $result = $this->getDatatable()
            ->deleteTable($table)
            ->query();
            
        return $result;
    }
    
    /**
     * 添加字段
     */
    public function createField($table, $attr = []) {
        $result = $this->getDatatable()
            ->columField($table, $attr, 'add')
            ->query();
            
        return $result;
    }
    
    /**
     * 更新字段
     */
    public function changeField($table, $attr = []) {
        $result = $this->getDatatable()
            ->columField($table, $attr, 'change')
            ->query();
            
        return $result;
    }
    
    /**
     * 删除字段
     */
    public function deleteField($table, $field = '') 
    {
        $result = $this->getDatatable()
            ->deleteField($table, $field)
            ->query();
            
        return $result;
    }
    
    /**
     * 添加默认字段
     */
    public function setDefaultField($table) 
    {
        $attrs = [
            [
                'name' => 'status', 
                'title' => '数据状态', 
                'type' => 'select', 
                'length' => 1, 
                'options' => "0:禁用\r\n1:正常", 
                'value'=>'1',
                'remark' => '数据状态', 
                'is_must' => 1, 
            ],
            [
                'name' => 'edit_time', 
                'after' => 'status', 
                'title' => '更新时间', 
                'type' => 'datetime', 
                'length' => 10, 
                'extra' => '', 
                'remark' => '更新时间', 
                'is_must' => 0, 
                'value'=> '0',
            ],
            [
                'name' => 'edit_ip', 
                'after' => 'edit_time', 
                'title' => '更新IP', 
                'type' => 'string', 
                'length' => 50, 
                'extra' => '', 
                'remark' => '更新IP', 
                'is_must' => 0, 
                'value'=> '',
            ],
            'add_time' => [
                'name' => 'add_time', 
                'after' => 'edit_ip', 
                'title' => '添加时间', 
                'type' => 'datetime', 
                'length' => 10, 
                'extra' => '', 
                'remark' => '添加时间', 
                'is_must' => 0, 
                'value'=> '0',
            ],
            'add_ip' => [
                'name' => 'add_ip', 
                'after' => 'add_time', 
                'title' => '添加IP', 
                'type' => 'string', 
                'length' => 50, 
                'extra' => '', 
                'remark' => '添加IP', 
                'is_must' => 0, 
                'value'=> '',
            ],
        ];
        foreach ($attrs as $attr) {
            $this->createField($table, $attr);
        }
        
        return true;
    }
    
}