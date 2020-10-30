<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\validate;

class Healthy extends Common
{

    //健康圈首页
    public function index()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list = Db::name('healthy')->order('heal_id desc')->paginate(10);
        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加健康圈
    public function addHealthy()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($this->request->isPost()) {
            $img = input('img/a');
           // if(is_array($img)){
            $img = implode('===', $img);
          //  }
            $head=input('head');
            if(empty($head)){
                $head='http://api.qingshishenghuo.shop/uploads/healthy/20200402/42cd6a37c298a8432038725220c24f95.png';
            }else{
                $head=$head;
            }
            $data = [
                'mobile' => input('mobile'),
                'content' => input('content'),
                'img' => $img,
                'create_time' => time(),
                'goods_id' => input('goods_id'),
                'status'=>'1',
                'head'=>$head,
                'user_id'=>158,
                'is_image'=>1,
                'sharenum'=> mt_rand(10,200),
            ];
          //  var_dump($data);die;
            $res = Db::name('healthy')->insert($data);
            if ($res) {
                $this->success('添加成功', 'Healthy/index', 3);
            } else {
                $this->error('添加失败');
            }
        } else {
            $goods_list=Db::name('goods')->where('goods_id','<>',100)->select();
            //   var_dump($good_list);die;
            $this->assign('goods_list',$goods_list);
            return $this->fetch();
        }
    }

    //健康圈详情
    public function HealDetail()
    {
        if ($this->request->isPost()) {

            $heal_id = input('heal_id');
            $list = Db::name('healthy')->where('heal_id', $heal_id)->find();
            $img = input('img/a');
            if (empty($img)) {
                $img = $list['img'];
            } else {
                $img = input('img/a');

                $img = implode('|', $img);
            }
            if(empty($head)){
                $head = $list['head'];
            }
            $create_time = $list['create_time'];
            $data = [
                'mobile' => input('mobile'),
                'content' => input('content'),
                'img' => $img,
                'create_time' => $create_time,
                'check_time' => time(),
                'reason' => input('reason'),
                'status' => input('status'),
                'head'=>$head,
            ];
            //var_dump($data);die;
            $res = Db::name('healthy')->where('heal_id',
                $heal_id)->update($data);
            if ($res) {
                $this->success('更新成功', 'Healthy/index', 3);
            } else {
                $this->error('更新失败');
            }
        } else {
            $heal_id = input('heal_id');
            $list = Db::name('healthy')->where('heal_id', $heal_id)->find();
         //   var_dump(  $list);die;
            $this->assign('list', $list);
            //$this->assign('content', $list['content']);
            $img = explode('===', $list['img']);
            $this->assign('img', $img);
            //var_dump( $img);die;
            return $this->fetch();
        }

    }

    //精选
    public function changeHeal()
    {
        $heal_id = input('heal_id');
        $list = Db::name('healthy')->where('heal_id', $heal_id)->find();
        if (empty($list)) {
            $this->error('该健康圈不存在');
        }

        $res = Db::name('healthy')->where('heal_id', $heal_id)->update(['is_choice' => 1]);
        if ($res) {
            $this->success('设置精选成功', 'Healthy/index', 3);
        } else {
            $this->error('设置失败，请联系管理员');
        }
    }
    //取消
    public function checkHeal()
    {
        $heal_id = input('heal_id');
        $list = Db::name('healthy')->where('heal_id', $heal_id)->find();
        if (empty($list)) {
            $this->error('该健康圈不存在');
        }

        $res = Db::name('healthy')->where('heal_id', $heal_id)->update(['is_choice' => 0]);
        if ($res) {
            $this->success('取消精选成功', 'Healthy/index', 3);
        } else {
            $this->error('取消精选失败，请联系管理员');
        }
    }

    //删除
    public function delHeal()
    {
        $heal_id = input('heal_id');
        $list = Db::name('healthy')->where('heal_id', $heal_id)->find();
        if (empty($list)){
            $this->error('该健康圈不存在');
        }
        $res = Db::name('healthy')->where('heal_id', $heal_id)->delete();
        if ($res) {
            $this->success('删除成功', 'Healthy/index', 3);
        } else {
            $this->error('删除失败，请联系管理员');
        }
    }
//查看所有评论
    public function seeheal()
    {
        $heal_id=input('heal_id');
        $list=Db::name('comment')->where('heal_id',$heal_id)->paginate('10');
        $count=$list->total();
        //var_dump( $list);die;
        $this->assign('list',$list);
        $this->assign('count',$count);
        return $this->fetch();

    }
    //删除评论
    public  function  delComment(){
        $comment_id = input('comment_id');
        $list = Db::name('comment')->where('comment_id',$comment_id)->find();
        if (empty($list)) {
            $this->error('该评论不存在');
        }
        $res = Db::name('comment')->where('comment_id', $comment_id)->delete();
        if ($res) {
            $this->success('删除成功', 'Healthy/index', 3);
        } else {
            $this->error('删除失败，请联系管理员');
        }
    }

    // 处理图片上传
    public function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $files = $request->file();
        //var_dump( $files);die;
        $validate = config('HEALTHY_VALIDATE');
        $path = config('HEALTHY_PATH');
        foreach ($files as $file) {
            $res = $file->validate($validate)->move($path);
            if ($res) {
                $file_path = $res->getPathname();
                return json(['status' => 'success', 'msg' =>config('server_name'). '/' . $file_path]);
            } else {
                return json(['status' => 'fail', 'msg' => $file->getError()]);
            }
        }
    }
}