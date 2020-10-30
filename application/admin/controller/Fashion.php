<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\validate;

class Fashion extends Common
{

    //会员等级首页
    public function level()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list = Db::name('fashion_level')->order('fash_level_id ')->paginate(10);

        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();

    }

    //添加会员等级
    public function addLevel()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($this->request->isPost()) {
            $data = [
                'name' => input('name'),
                'num' => input('num'),
                'create_time' => time(),
            ];
            //var_dump(  $data);die;
            $res = Db::name('fashion_level')->insert($data);
            if ($res) {
                $this->success('添加成功', 'fashion/level', 3);
            } else {
                $this->error('添加失败');
            }
        } else {
            return $this->fetch();
        }
    }

    //修改等级
    public function editLevel()
    {
        $fash_level_id = input('fash_level_id');
        if ($this->request->isPost()) {

            $data = [
                'name' => input('name'),
                'num' => input('num'),
            ];
            //  var_dump($data);die;
            $res = Db::name('fashion_level')->where('fash_level_id',
                $fash_level_id)->update($data);
            if ($res) {
                $this->success('更新成功', 'fashion/level', 3);
            } else {
                $this->error('更新失败');
            }
        } else {
            $list = Db::name('fashion_level')->where('fash_level_id', $fash_level_id)->find();
            $this->assign('list', $list);
            return $this->fetch();
        }

    }

    //删除等级
    public function delLevel()
    {
        $fash_level_id = input('fash_level_id');
        $list = Db::name('fashion_level')->where('fash_level_id', $fash_level_id)->find();
        if (empty($list)) {
            $this->error('该等级不存在');
        }
        $res = Db::name('fashion_level')->where('fash_level_id', $fash_level_id)->delete();
        if ($res) {
            $this->success('删除成功', 'fashion/level', 3);
        } else {
            $this->error('删除失败，请联系管理员');
        }
    }

    //达人规则
    public function addRule()
    {
        if ($this->request->isPost()) {
            $data = [
                'step' => input('step'),
                'low' => input('low'),
                'high' => input('high'),
                'three' => input('three'),
                'everyday' => input('everyday'),
                //'five' => input('five'),
                'seven' => input('seven'),
                 'max_power' => input('max_power'),
            ];
            $rule_id = input('rule_id');
            if ($rule_id == 1) {
                $res = Db::name('rule')->where('rule_id', 1)->update($data);
                if ($res) {
                    $this->success('更新成功', 'fashion/addRule', 3);
                } else {
                    $this->error('系统错误,请联系管理员!');
                }
            } else {
                $res = Db::name('rule')->insert($data);
                if ($res) {
                    $this->success('添加成功', 'fashion/addRule', 3);
                } else {
                    $this->error('添加失败');
                }
            }

        } else {
            $list = Db::name('rule')->where('rule_id', 1)->find();
            $this->assign('list', $list);
            return $this->fetch();
        }
    }

    //摘取记录
    public function getStep()
    {
        $list = Db::name('getstep')->order('getstep_id desc')->paginate('10');
        $this->assign('list', $list);
        $count = $list->total();
        $this->assign('count', $count);
        return $this->fetch();
    }

    //加油记录
    public function sendStep()
    {
        $list = Db::name('sendoil')->order('sendoil_id desc')->paginate('10');
        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }
}