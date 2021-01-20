<?php

namespace app\admin\controller;

use think\helper\Arr;

use Lake\TTree as Tree;

use app\lakecms\support\Validate;
use app\lakecms\model\Category as CategoryModel;
use app\lakecms\model\Model as ModelModel;
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
        $cate = CategoryModel::where([
                ['status', '=', 1], 
            ])
            ->order("sort ASC, id DESC")
            ->select()
            ->toArray();
        
        $newCategory = [];
        foreach ($cate as $cate) {
            $data = [
                'id' => $cate['id'],
                'parentid' => $cate['parentid'],
                'title' => $cate['title'],
                'type' => $cate['type'],
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
     * 单页
     */
    public function page() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $id = $data['id'];
            if (empty($id)) {
                $this->error("信息ID不能为空！");
            }
            unset($data['id']);
            
            $cateid = request()->param('cateid');
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 2,
                    'status' => 1,
                ])
                ->find();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ModelModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 1);
            
            // 验证
            $validate = new Validate();
            $validate->withRules(Arr::get($validateFields, 'rule', []));
            $validate->withMessages(Arr::get($validateFields, 'message', []));
            $validate->withScenes(Arr::get($validateFields, 'scene', []));
            $validate->scene('update');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = $model['fields'];
            $data['modelField'] = ModelModel::formatFormFields($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $table = $cate['model']['tablename'];
            $data = $data['modelField'];
            $where = [
                ['id', '=', $id],
            ];
            
            $result = ContentModel::newUpdate($table, $data, $where);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 关联标签
            if (isset($data['modelField'])) {
                $tags = ModelModel::formatFormFieldTags($fields, $data['modelField']);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], $cateid, $id);
                }
            }
            
            return $this->success('修改成功！');
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 2,
                    'status' => 1,
                ])
                ->find();
            
            $info = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'categoryid' => $cateid,
                ])
                ->order('id ASC')
                ->find();
            if (empty($info)) {
                $createData['categoryid'] = $cateid;
                ContentModel::newCreate($cate['model']['tablename'], $createData);
            }
            $this->assign("info", $info);
            
            $modelField = ModelModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 1);
            foreach ($modelField as $key => $value) {
                if (isset($info[$value['name']])) {
                    $modelField[$key]['value'] = $info[$value['name']];
                }
                
                if ($value['type'] == 'datetime') {
                    $modelField[$key]['value'] = date('Y-m-d H:i:s', $info[$value['name']]);
                }
            }
            $this->assign("fieldList", $modelField);
            
            return $this->fetch();
        }
    }

    /**
     * 列表
     */
    public function cate() 
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $keyword = $this->request->param('keyword/s', '', 'trim');
            
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
            foreach ($modelField as $field) {
                if ($field['is_filter'] == 1) {
                    $map[] = [$field['name'], 'like', "%$keyword%"];
                }
            }
            
            $query = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    ['categoryid', '=', $cate['id']],
                ])
                ->where($map);
            $queryCount = clone $query;
            $data = $query->order("id DESC")
                ->page($page, $limit)
                ->select()
                ->toArray();
            $total = $queryCount->count();
            
            foreach ($data as $key => $item) {
                $data[$key]['url'] = (string) url('lakecms/content/index', [
                    'cateid' => $cate['id'],
                    'id' => $item['id'],
                ]);
            }

            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            
            return json($result);
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            $this->assign("cate", $cate);
            
            return $this->fetch();
        }
    }

    /**
     * 添加
     */
    public function add() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $cateid = $this->request->param('cateid', 0);
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ModelModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 2);
            
            // 验证
            $validate = new Validate();
            $validate->withRules(Arr::get($validateFields, 'rule', []));
            $validate->withMessages(Arr::get($validateFields, 'message', []));
            $validate->withScenes(Arr::get($validateFields, 'scene', []));
            $validate->scene('create');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = $model['fields'];
            $data['modelField'] = ModelModel::formatFormFields($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $data['modelField']['categoryid'] = $cateid;
            $result = ContentModel::newCreate($cate['model']['tablename'], $data['modelField']);
            if (false === $result) {
                return $this->error('添加失败！');
            }
            
            // 关联标签
            if (isset($data['modelField'])) {
                $tags = ModelModel::formatFormFieldTags($fields, $data['modelField']);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], $cateid, $result->id);
                }
            }
            
            return $this->success('添加成功！');
        } else {
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $modelField = ModelModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 1);
            $this->assign("fieldList", $modelField);
            
            return $this->fetch();
        }
    }

    /**
     * 编辑
     */
    public function edit() 
    {
        if (request()->isPost()) {
            $data = $this->request->post();
            
            $id = request()->param('id');
            if (empty($id)) {
                $this->error("信息ID不能为空！");
            }
            
            $cateid = request()->param('cateid');
            if (empty($cateid)) {
                $this->error("请指定栏目ID！");
            }
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            if (empty($cate)) {
                $this->error('该栏目不存在！');
            }
            
            $validateFields = ModelModel::validateFields([
                'modelid' => $cate['model']['id'],
                'status' => 1,
            ], 3);
            
            // 验证
            $validate = new Validate();
            $validate->withRules(Arr::get($validateFields, 'rule', []));
            $validate->withMessages(Arr::get($validateFields, 'message', []));
            $validate->withScenes(Arr::get($validateFields, 'scene', []));
            $validate->scene('update');
            
            $model = ModelModel::where([
                    'id' => $cate['model']['id'],
                    'status' => 1,
                ])->find();
            $fields = collect($model['fields'])->toArray();
            $data['modelField'] = ModelModel::formatFormFields($fields, $data['modelField']);
            
            $result = $this->validate($data['modelField'], $validate, []);
            if (true !== $result) {
                return $this->error($result);
            }
            
            $table = $cate['model']['tablename'];
            $data = $data['modelField'];
            $where = [
                ['id', '=', $id],
            ];
            
            $result = ContentModel::newUpdate($table, $data, $where);
            if (false === $result) {
                return $this->error('修改失败！');
            }
            
            // 关联标签
            if (isset($data)) {
                $tags = ModelModel::formatFormFieldTags($fields, $data);
            
                foreach ($tags as $tag) {
                    ContentModel::updateTagsContent($tag, $cate['model']['id'], $cateid, $id);
                }
            }
            
            return $this->success('修改成功！');
        } else {
            $id = request()->param('id');
            
            $cateid = $this->request->param('cateid', 0);
            $this->assign("cateid", $cateid);
            
            $cate = CategoryModel::with(['model'])
                ->where([
                    'id' => $cateid,
                    'type' => 1,
                    'status' => 1,
                ])
                ->find()
                ->toArray();
            
            $info = ContentModel::newTable($cate['model']['tablename'])
                ->where([
                    'id' => $id,
                    'categoryid' => $cateid,
                ])
                ->find();
                
            $modelField = ModelModel::formFields([
                    'id' => $cate['modelid'],
                    'status' => 1,
                ], 1);
            foreach ($modelField as $key => $value) {
                if (isset($info[$value['name']])) {
                    $modelField[$key]['value'] = $info[$value['name']];
                }
                
                if ($value['type'] == 'datetime') {
                    $modelField[$key]['value'] = date('Y-m-d H:i:s', $info[$value['name']]);
                }
            }
            $this->assign("fieldList", $modelField);
            
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
        
        $cateid = $this->request->param('cateid', 0);
        if (! $cateid) {
            return $this->error("非法操作！");
        }
        
        $ids = request()->param('ids/a');
        if (! $ids) {
            return $this->error("非法操作！");
        }
        
        $cate = CategoryModel::with(['model'])
            ->where([
                'id' => $cateid,
                'type' => 1,
                'status' => 1,
            ])
            ->find()
            ->toArray();
        
        $result = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', 'in', $ids],
                ['categoryid', '=', $cateid],
            ])
            ->delete();
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
        
        $cateid = $this->request->param('cateid', 0);
        if (! $cateid) {
            return $this->error("非法操作！");
        }
        
        $id = request()->param('id/d');
        if (! $id) {
            return $this->error("非法操作！");
        }
        
        $cate = CategoryModel::with(['model'])
            ->where([
                'id' => $cateid,
                'type' => 1,
                'status' => 1,
            ])
            ->find()
            ->toArray();
        
        $status = input('status', '0', 'trim,intval');

        $result = ContentModel::newTable($cate['model']['tablename'])
            ->where([
                ['id', '=', $id],
                ['categoryid', '=', $cateid],
            ])
            ->update([
                'status' => $status,
            ]);
        if (false === $result) {
            return $this->error("设置失败！");
        }
        
        return $this->success("设置成功！");
    } 
    
}