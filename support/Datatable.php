<?php

namespace app\lakecms\support;

use think\facade\Db;

/**
 * 数据库管理类
 */
class Datatable 
{
    /* 数据库操作的表 */
    protected $table; 
    
    /* 数据库操作字段 */
    protected $fields = []; 
    
    /* 数据库操作字符集 */
    protected $charset = 'utf8mb4'; 
    
    /* 数据库操作表前缀 */
    protected $prefix = ''; 
    
    /* 数据库引擎 */
    protected $engineType = 'MyISAM'; 
    
    /* 数据库主键 */
    protected $key = 'id'; 
    
    /* 最后生成的sql语句 */
    public $sql = ''; 
    
    /* 类型列表 */
    protected $types        = [
        "TINYINT",
        "INT",
        "SMALLINT",
        "MEDIUMINT",
        "DATETIME",
        "CHAR",
        "VARCHAR",
        "TINYTEXT",
        "TEXT",
        "MEDIUMTEXT",
        "LONGTEXT",
        "NUMERIC",
        "DECIMAL",
        "ENUM",
        "TINYBLOB",
        "BLOB",
        "MEDIUMBLOB",
        "LONGBLOB",
    ];

    /**
     * 设置表前缀
     */
    public function setPrefix($prefix = '') 
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * 设置表名
     */
    public function setTable($table = '') 
    {
        $this->table = $this->getTablename($table);
        return $this;
    }

    /**
     * 数据库操作字符集
     */
    public function setCharset($charset = 'utf8mb4') 
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * 数据库引擎
     */
    public function setEngineType($type = 'MyISAM') 
    {
        $this->engineType = $type;
        return $this;
    }

    /**
     * 设置字段类型列表
     */
    public function setTypes($types = [], $force = false) 
    {
        if ($force) {
            $this->types = $types;
        } else {
            $this->types = array_merge($this->types, $types);
        }
        
        return $this;
    }

    /**
     * 创建表
     */
    public function createTable(
        $table = '', 
        $comment = '', 
        $pk = 'id', 
        $charset = null, 
        $engineType = null
    ) {
        $this->setTable($table);

        $sql = $this->generateField($pk, 'int', 11, '', '主键', true);

        $primary = $pk ? "PRIMARY KEY (`" . $pk . "`)" : '';
        $generatesql = $sql . ',';
        
        if (empty($engineType)) {
            $engineType = $this->engineType;
        }
        
        if (empty($charset)) {
            $charset = $this->charset;
        }

        $create = "CREATE TABLE IF NOT EXISTS `" . $this->table . "`("
            . $generatesql
            . $primary
            . ") ENGINE=" . $engineType . " AUTO_INCREMENT=1 DEFAULT CHARSET=" . $charset . " ROW_FORMAT=DYNAMIC COMMENT='" . $comment . "';";
        
        $this->sql = $create;
        
        return $this;
    }

    /**
     * 快速创建ID字段
     */
    public function generateField(
        $key = '', 
        $type = '', 
        $length = 11, 
        $default = '', 
        $comment = '主键', 
        $is_auto_increment = false
    ) {
        if ($key && $type) {
            $auto_increment = $is_auto_increment ? 'AUTO_INCREMENT' : '';
            $field_type     = $length ? $type . '(' . $length . ')' : $type;
            $signed         = in_array($type, array('int', 'float', 'double')) ? 'signed' : '';
            $comment        = $comment ? "COMMENT '" . $comment . "'" : "";
            $default        = $default ? "DEFAULT '" . $default . "'" : "";
            $sql            = "`{$key}` {$field_type} {$signed} NOT NULL {$default} $auto_increment {$comment}";
        }
        
        return $sql;
    }

    /**
     * 字段
     * 
     * param $table 表名
     * param $attr = [
     *      table, 
     *      name, type, length,
     *      is_must, value, remark,
     *      is_must, value, after
     * ]
     * param $action 方式
     */
    public function columField($table, $attr = [], $action = 'add') 
    {
        $field_attr['table'] = $table ? $this->getTablename($table) : $this->table;
        $field_attr['name'] = $attr['name'];

        if (empty($attr['define'])) {
            if (! in_array($attr['type'], $this->types)) {
                $field_attr['type'] = $attr['type'];
            } else {
                $field_attr['type'] = 'VARCHAR';
            }
            
            if (intval($attr['length']) && $attr['length']) {
                $field_attr['length'] = "(" . $attr['length'] . ")";
            } else {
                $field_attr['length'] = "";
            }
            
            $field_attr['is_null'] = $attr['is_must'] ? 'NOT NULL' : 'NULL';
            $field_attr['default'] = $attr['value'] != '' ? 'DEFAULT "' . $attr['value'] . '"' : '';
            
            $field_attr['define'] = "{$field_attr['type']}{$field_attr['length']} {$field_attr['is_null']} {$field_attr['default']}";
        }

        $field_attr['comment'] = (isset($attr['remark']) && $attr['remark']) ? $attr['remark'] : $attr['title'];
        $field_attr['after']   = (isset($attr['after']) && $attr['after']) ? ' AFTER `' . $attr['after'] . '`' : ' AFTER `id`';
        $field_attr['action']  = (isset($attr['action']) && $attr['action']) ? $attr['action'] : 'ADD';
        
        // 执行方式
        if ($action == 'add') {
            $this->sql = "ALTER TABLE `{$field_attr['table']}` ADD `{$field_attr['name']}` {$field_attr['define']} COMMENT '{$field_attr['comment']}' {$field_attr['after']}";
        } elseif ($action == 'change') {
            $field_attr['oldname'] = (isset($attr['oldname']) && $attr['oldname']) ? $attr['oldname'] : '';

            $this->sql = "ALTER TABLE `{$field_attr['table']}` CHANGE `{$field_attr['oldname']}` `{$field_attr['name']}` {$field_attr['define']} COMMENT '{$field_attr['comment']}'";
        }
        
        return $this;
    }

    /**
     * 删除字段
     * 
     * @var $table 表名
     * @var $field 字段名
     */
    public function deleteField($table, $field) 
    {
        $table = $table ? $this->getTablename($table) : $this->table;
        $this->sql = "ALTER TABLE `$table` DROP `$field`";
        return $this;
    }

    /**
     * 删除数据表
     * 
     * @var $table 表名
     */
    public function deleteTable($table) 
    {
        $table = $table ? $this->getTablename($table) : $this->table;
        $this->sql = "DROP TABLE IF EXISTS `$table`";
        return $this;
    }

    /**
     * 更新数据表
     * 
     * @var $table 表名
     */
    public function updateTableName($old_table = '', $new_table = '') 
    {
        if (!empty($old_table) && !empty($new_table)) {
            $old_table = $this->getTablename($old_table);
            $new_table = $this->getTablename($new_table);
            $this->sql = "RENAME TABLE  `".$old_table."` TO  `".$new_table."` ;";
        }
        
        return $this;
    }
    
    /**
     * create的别名
     * @return boolen 
     */
    public function query() 
    {
        if (empty($this->sql)) {
            return false;
        }
        
        $res = Db::execute($this->sql);
        return $res !== false;
    }

    /**
     * 创建动作
     * @return boolen 
     */
    public function create() 
    {
        return $this->query();
    }

    /**
     * 获取最后生成的sql语句
     */
    public function getLastSql() 
    {
        return $this->sql;
    }

    /**
     * 获取指定的表名
     * @var $table 要获取名字的表名
     * @var $prefix 获取表前缀, 默认为不获取 false
     */
    public function getTablename($table) 
    {
        $this->table = $this->prefix . $table;
        return $this->table;
    }

    /**
     * 获取指定表名的所有字段及详细信息
     * 
     * @var $table 要获取名字的表名
     */
    public function getFields($table = false) 
    {
        if (false == $table) {
            $table = $this->table; //为空调用当前table
        } else {
            $table = $table;
        }
        
        $patten = "/\./";
        if (!preg_match_all($patten, $table)) {
            //匹配_
            $patten = "/_+/";
            if (!preg_match_all($patten, $table)) {
                $table = $this->prefix . $table;
            } else {
                //匹配是否包含表前缀，如果是 那么就是手动输入
                $patten = "/$this->prefix/";
                if (!preg_match_all($patten, $table)) {
                    $table = $this->prefix . $table;
                }
            }
        }
        
        $sql = "SHOW FULL FIELDS FROM $table";
        return Db::query($sql);
    }

    /**
     * 确认表是否存在
     * @var $table 表名
     */
    public function checkTable($table) 
    {
        //获取表名
        $this->table = $this->getTablename($table);
        $result = Db::execute("SHOW TABLES LIKE '%$this->table%'");
        return $result;
    }

    /**
     * 确认字段是否存在
     * @var $table 表名 
     * @var $field 字段名 要检查的字段名
     */
    public function checkField($table, $field) 
    {
        // 检查字段是否存在
        $table = $this->getTablename($table);
        if (! Db::query("Describe $table $field")) {
            return false;
        }
        
        return true;
    }
}