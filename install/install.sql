DROP TABLE IF EXISTS `pre__lakecms_category`;
CREATE TABLE `pre__lakecms_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `modelid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '唯一标识',
  `title` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '栏目名称',
  `keywords` varchar(250) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '关键字',
  `description` mediumtext CHARACTER SET utf8mb4 COMMENT '栏目描述',
  `cover` char(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '栏目图片',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-列表，2-单页',
  `template_list` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '列表模板',
  `template_detail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '详情模板',
  `template_page` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '编辑模板',
  `index_url` text CHARACTER SET utf8mb4 COMMENT '栏目链接地址',
  `content_url` text CHARACTER SET utf8mb4 COMMENT '内容链接地址',
  `order_list` varchar(100) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '排序字段字符',
  `list_grid` text COLLATE utf8mb4_unicode_ci COMMENT '列表定义',
  `pagesize` int(5) DEFAULT '10' COMMENT '每页数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，1-启用',
  `edit_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  `is_inchildren` tinyint(1) DEFAULT '0' COMMENT '状态，1-启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='栏目表';

DROP TABLE IF EXISTS `pre__lakecms_model`;
CREATE TABLE `pre__lakecms_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `tablename` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '表名',
  `comment` varchar(200) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '描述',
  `sort` int(10) DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，1-启用',
  `edit_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模型列表';

DROP TABLE IF EXISTS `pre__lakecms_model_field`;
CREATE TABLE `pre__lakecms_model_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modelid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段注释',
  `length` varchar(100) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '字段长度',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数据类型',
  `options` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '参数',
  `value` varchar(100) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '备注',
  `validate_rule` text CHARACTER SET utf8mb4 COMMENT '验证规则',
  `validate_message` text CHARACTER SET utf8mb4 COMMENT '验证返回信息，换行分割',
  `validate_time` varchar(10) CHARACTER SET utf8mb4 DEFAULT 'create' COMMENT '验证事件，create-添加，update-编辑，always-始终，或者自定义',
  `show_type` tinyint(1) DEFAULT '4' COMMENT '显示类型，1-全部显示，2-添加显示，3-编辑显示，4-都不显示',
  `is_filter` tinyint(1) DEFAULT '0' COMMENT '筛选字段',
  `is_must` tinyint(1) DEFAULT '0' COMMENT '是否必填',
  `is_show` tinyint(1) DEFAULT '0' COMMENT '是否显示',
  `is_list_show` tinyint(1) DEFAULT '0' COMMENT '是否列表显示',
  `is_detail_show` tinyint(1) DEFAULT '0' COMMENT '是否详情显示',
  `is_view` tinyint(1) DEFAULT '0' COMMENT '是否阅读量',
  `sort` int(10) DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1-启用',
  `edit_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='模型字段列表';

DROP TABLE IF EXISTS `pre__lakecms_navbar`;
CREATE TABLE `pre__lakecms_navbar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `url` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '链接地址',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '链接描述',
  `target` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self' COMMENT '跳转方式，_self-自身，_blank-跳出',
  `sort` int(10) DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，1-启用',
  `edit_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='tags主表';

DROP TABLE IF EXISTS `pre__lakecms_settings`;
CREATE TABLE `pre__lakecms_settings` (
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '配置值',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '配置说明',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配置';

DROP TABLE IF EXISTS `pre__lakecms_tags`;
CREATE TABLE `pre__lakecms_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `keywords` tinytext CHARACTER SET utf8mb4 COMMENT '关键字',
  `description` mediumtext CHARACTER SET utf8mb4 COMMENT '描述',
  `views` mediumint(8) unsigned DEFAULT '0' COMMENT '点击数',
  `sort` int(10) DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1-启用',
  `edit_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `edit_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `views` (`views`,`sort`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='tags主表';

DROP TABLE IF EXISTS `pre__lakecms_tags_content`;
CREATE TABLE `pre__lakecms_tags_content` (
  `tagid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '标签ID',
  `modelid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `cateid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `contentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '信息ID',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  KEY `tag` (`tagid`),
  KEY `modelid` (`contentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='tags数据表';
