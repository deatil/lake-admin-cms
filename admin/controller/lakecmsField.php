<?php

namespace app\admin\controller;

use Lake\TTree;
use Lake\Admin\Model\FieldType as FieldTypeModel;

use app\lakecms\service\Datatable;
use app\lakecms\model\Model as ModelModel;
use app\lakecms\model\ModelField as ModelFieldModel;

/**
 * 模型
 *
 * @create 2020-1-7
 * @author deatil
 */
class LakecmsField extends LakecmsBase 
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
            
            $data = ModelFieldModel::where($map)
                ->order("sort ASC, id ASC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = ModelFieldModel::where($map)
                ->count();

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return json($result);
        } else {
            $table = $this->request->param('table/s');
            
            // 模型详情
            $model = ModelModel::where([
                'tablename' => $table,
            ])->find();

            $data = [
                'tablename' => $table,
                'model' => $model,
            ];
            $this->assign($data);
            
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
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\ModelField.add');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $result = ModelFieldModel::create($data);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            return $this->success('添加成功！');
        } else {
            $table = $this->request->param('table/s');
            $this->assign("tablename", $table);
            
            $fieldType = FieldTypeModel::order('listorder')
                ->column('name,title,default_define,ifoption,ifstring');
            $this->assign("fieldType", $fieldType);
            
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
            $validate = $this->validate($data, '\\app\\lakecms\\validate\\ModelField.edit');
            if (true !== $validate) {
                return $this->error($validate);
            }
            
            $id = request()->post('id');
            if (empty($id)) {
                return $this->error('ID错误');
            }
            
            $info = ModelFieldModel::where([
                'id' => $id,
            ])->find();
            if (empty($info)) {
                return $this->error('表单不存在');
            }
            
            $data = request()->post();
            
            $result = ModelFieldModel::where([
                    'id' => $id,
                ])
                ->update($data);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->get('id');
            
            $info = ModelFieldModel::where([
                'id' => $id,
            ])->find();
            $this->assign("info", $info);
            
            // 模型信息
            $table = $this->request->param('table/s');
            $this->assign("tablename", $table);
            
            $fieldType = FieldTypeModel::order('listorder')
                ->column('name,title,default_define,ifoption,ifstring');
            $this->assign("fieldType", $fieldType);
            
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
        
        $data = ModelFieldModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('数据不存在！');
        }
        
        $result = ModelFieldModel::where([
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

        $result = ModelFieldModel::where([
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
        
        $result = ModelFieldModel::where([
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