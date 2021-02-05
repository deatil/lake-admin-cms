## cms内容管理系统


### 项目介绍

*  基于lake-admin后台管理框架的内容管理模块


### 安装使用

*  composer安装
    * `composer require lake/lake-admin-cms`
*  本地安装
    * 后台 `本地模块->模块管理->本地安装` 上传模块或者手动将模块上传到 `/addon` 目录
    * 手动放置模块或者上传模块请确保目录为 `lakecms/Service.php`
*  最后在 `本地模块->模块管理->全部` 安装添加的模块
*  当前模块所需依赖模块： `广告模块`、`自定义表单` 及 `友情链接`
*  前台访问：`http://yourdomain/lakecms`


截图预览

![LakeAdmin](https://user-images.githubusercontent.com/24578855/106987325-294cd580-67a8-11eb-8ca5-b4bd8323847b.png)

![LakeAdmin2](https://user-images.githubusercontent.com/24578855/106987335-2e118980-67a8-11eb-8cfb-84c52bfe73ce.png)

![LakeAdmin3](https://user-images.githubusercontent.com/24578855/106987717-08d14b00-67a9-11eb-85f7-59d0259f6517.png)

### 模块推荐

| 名称 | 描述 |
| --- | --- |
| [cms系统](https://github.com/deatil/lake-admin-cms) | 简单高效实用的内容管理系统 |
| [用户管理](https://github.com/deatil/lake-admin-addon-luser) | 通用的用户管理模块，实现了用户登陆api的token及jwt双认证 |
| [API接口](https://github.com/deatil/lake-admin-addon-lapi) | 强大的API接口管理系统，支持多种签名算法验证，支持签名字段多个位置存放 |
| [路由美化](https://github.com/deatil/lake-admin-addon-lroute) | 支持thinkphp自带的多种路由美化设置，自定义你的系统url |
| [菜单结构](https://github.com/deatil/lake-admin-addon-lmenu) | 提取后台菜单分级结构格式，为你的模块开发保驾护航 |
| [数据库管理](https://github.com/deatil/lake-admin-addon-database) | 数据库备份、优化、修复及还原，你的系统维护帮手 |
| [广告模块](https://github.com/deatil/lake-admin-ad) | cms模块必备 |
| [自定义表单](https://github.com/deatil/lake-admin-form) | cms模块必备模块 |
| [友情链接](https://github.com/deatil/lake-admin-friendlink) | cms模块必备模块 |


## 问题反馈

在使用中有任何问题，请使用以下联系方式联系我们

Github: https://github.com/deatil/lake-admin-cms


## 版权信息

本模块遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有 Copyright © deatil(https://github.com/deatil)

All rights reserved。
