<?php

namespace app\admin\controller;

use Lake\TTree;

use app\lakecms\service\Model;
use app\lakecms\service\Datatable;
use app\lakecms\model\Category as CategoryModel;

/**
 * 栏目
 *
 * @create 2020-1-7
 * @author deatil
 */
class LakecmsCategory extends LakecmsBase 
{    
    /**
     * 列表
     */
    public function index() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $data = CategoryModel::where($map)
                ->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = CategoryModel::where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {
            return $this->fetch();
        }
    }

    /**
     * 结构列表
     */
    public function tree() 
    {
        if ($this->request->isAjax()) {
            $result = CategoryModel::order([
                    'sort' => 'ASC', 
                    'id' => 'ASC',
                ])
                ->select()
                ->toArray();

            $TTree = new TTree();
            $menuTree = $TTree->withData($result)->buildArray(0);
            $list = $TTree->buildFormatList($menuTree, 'title');
            $total = count($list);
            
            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $list
            ];

            return json($result);
        } else {
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
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Category.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = CategoryModel::insert($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            $parentid = $this->request->param('parentid', 0);
            
            $parents = CategoryModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $TTree = new TTree();
            $parentTree = $TTree->withData($parents)->buildArray(0);
            $parents = $TTree->buildFormatList($parentTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("parents", $parents);
            
            $template = Model::create()->withPath(lakecms_theme_path());
            $this->assign([
                'listTemplates' => $template->listTemplates(),
                'createTemplates' => $template->createTemplates(),
                'updateTemplates' => $template->updateTemplates(),
            ]);
            
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
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Category.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('表单不存在');
            }
            
            $data = request()->post();
            
            $result = CategoryModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = CategoryModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            $parentid = $info['parentid'];
            
            $parents = CategoryModel::order([
                'sort', 
                'id' => 'ASC',
            ])->select()->toArray();
            
            $TTree = new TTree();
            
            $childsId = $TTree->getListChildsId($parents, $info['id']);
            $childsId[] = $info['id'];
            
            $newParents = [];
            foreach ($parents as $r) {
                if (in_array($r['id'], $childsId)) {
                    continue;
                }
                
                $newParents[] = $r;
            }
            
            $parentTree = $TTree->withData($newParents)->buildArray(0);
            $parents = $TTree->buildFormatList($parentTree, 'title');
            
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
        
        $data = CategoryModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $children = CategoryModel::where([
            'parentid' => $id,
        ])->count();
        if ($children > 0) {
            return $this->error('当前导航数据还有子导航，暂不能删除！');
        }
        
        $result = CategoryModel::where([
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

        $result = CategoryModel::where([
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
        
        $result = CategoryModel::where([
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