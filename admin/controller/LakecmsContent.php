<?php

namespace app\admin\controller;

use Lake\TTree as Tree;

use app\lakecms\model\Category as CategoryModel;
use app\lakecms\model\ModelField as ModelFieldModel;
use app\lakecms\model\Content as ContentModel;

/**
 * 内容
 *
 * @create 2020-1-10
 * @author deatil
 */
class LakecmsContent extends LakecmsBase 
{    
    /**
     * 列表
     */
    public function index() 
    {
        $category = CategoryModel::where([
                ['status', '=', 1], 
            ])
            ->order("sort ASC, id DESC")
            ->select()
            ->toArray();
        
        $newCategory = [];
        foreach ($category as $cate) {
            $data = [
                'id' => $cate['id'],
                'parentid' => $cate['parentid'],
                'title' => $cate['title'],
                'field' => 'id',
                'spread' => true,
            ];
            $newCategory[] = $data;
        }
        
        $newCategory = (new Tree)
            ->withConfig('buildChildKey', 'children')
            ->withData($newCategory)
            ->buildArray(0);

        $this->assign("category", $newCategory);
        
        return $this->fetch();
    }

    /**
     * 内容首页
     */
    public function main() 
    {
        // 单页数量
        $pages = CategoryModel::where([
                'type' => 2,
                'status' => 1,
            ])
            ->count();
        $this->assign("pages", $pages);
        
        // 列表数量
        $cates = CategoryModel::with(['model'])
            ->where([
                'type' => 1,
                'status' => 1,
            ])
            ->order('sort ASC, id ASC')
            ->select()
            ->toArray();
        
        $newCates = [];
        foreach ($cates as $cate) {
            $cate['count'] = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'categoryid' => $cate['id'],
                    'status' => 1,
                ])
                ->count();
            $newCates[] = $cate;
        }
        $this->assign("cates", $newCates);
        
        return $this->fetch();
    }

    /**
     * 列表/单页
     */
    public function cate() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $cateid = $this->request->param('cateid', 0);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $modelField = ModelFieldModel::where([
                    'modelid' => $cate['model']['id'],
                    'status' => 1,
                ])
                ->order('sort ASC, id ASC')
                ->select()
                ->toArray();
            
            $query = ContentModel::newTable($cate['model']['tablename'])
                ->where($map);
            $data = $query->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $query->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
                "fields" => $modelField,
            ];
            
            return json($result);
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            return $this->fetch();
        }
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Navbar.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = NavbarModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            $parentid = $this->request->param('parentid', 0);
            
            $parents = NavbarModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            $parenTree = $Tree->withData($parents)->buildArray(0);
            $parents = $Tree->buildFormatList($parenTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            return $this->fetch();
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Navbar.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = NavbarModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('数据不存在');
            }
            
            $result = NavbarModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = NavbarModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            $parentid = $info['parentid'];
            
            $parents = NavbarModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            
            $childsId = $Tree->getListChildsId($parents, $info['id']);
            $childsId[] = $info['id'];
            
            $newParents = [];
            foreach ($parents as $r) {
                if (in_array($r['id'], $childsId)) {
                    continue;
                }
                
                $newParents[] = $r;
            }
            
            $parenTree = $Tree->withData($newParents)->buildArray(0);
            $parents = $Tree->buildFormatList($parenTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function delete() 
    {
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $data = NavbarModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $children = NavbarModel::where([
            'parentid' => $id,
        ])->count();
        if ($children > 0) {
            return $this->error('当前导航数据还有子导航，暂不能删除！');
        }
        
        $result = NavbarModel::where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        return $this->success('删除成功！');
    }
    
    /**
     * 修改状态
     */
    public function state() 
    {
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $status = input('status', '0', 'trim,intval');

        $result = NavbarModel::where([
                'id' => $id,
            ])
            ->update([
                'status' => $status,
            ]);
        if (false === $result) {
            return $this->error("设置失败！");
        }
        
        return $this->success("设置成功！");
    } 

    /**
     * 排序
     */
    public function sort()
    {
        if (! request()->isPost()) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $sort = $this->request->param('value/d', 100);
        
        $result = NavbarModel::where([
            'id' => $id,
        ])->update([
            'sort' => $sort,
        ]);
        
        if (false === $result) {
            return $this->error("排序失败！");
        }
        
        return $this->success("排序成功！");
    }
    
}