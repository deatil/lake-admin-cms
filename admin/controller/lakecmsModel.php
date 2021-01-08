<?php

namespace app\admin\controller;

use Lake\TTree;

use app\lakecms\service\Model as ModelService;
use app\lakecms\model\Model as ModelModel;

/**
 * 模型
 *
 * @create 2020-1-7
 * @author deatil
 */
class lakecmsModel extends LakecmsBase 
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
            
            $data = ModelModel::where($map)
                ->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = ModelModel::where($map)
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
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = request()->post();
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Model.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = ModelModel::insert($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            // 创建表
            $table = ModelService::create();
            $table->createTable($data['tablename'], $data['comment']);
            $table->setDefaultField($data['tablename']);
            
            return $this->success('添加成功！');
        } else {
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
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\Model.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = ModelModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('数据不存在');
            }
            
            $data = request()->post();
            
            $result = ModelModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 修改表名
            if ($info['tablename'] != $data['tablename']) {
                ModelService::create()->updateTableName($info['tablename'], $data['tablename']);
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = ModelModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
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
        
        $data = ModelModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }

        
        $result = ModelModel::where([
            'id' => $id,
        ])->delete();
        if (false === $result) {
            return $this->error('删除失败！');
        }
        
        // 删除表
        ModelService::create()->deleteTable($data['tablename']);
        
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

        $result = ModelModel::where([
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
        
        $result = ModelModel::where([
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