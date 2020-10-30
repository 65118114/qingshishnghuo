<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Level extends Common{

    //会员等级首页
    public  function  index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('level')->order('level_id ')->paginate(10);

        $count = $list->total();
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();

    }

    //添加会员等级
    public  function  addLevel(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($this->request->isPost()) {


            $data = [
                'name'=>input('name'),
                'pricesum'=>input('pricesum'),
                'vipsum'=>input('vipsum'),
                'svipsum'=>input('svipsum'),
                't1'=>input('t1'),
                't2'=>input('t2'),
                'status'=>input('status'),
                'create_time'=>time(),
            ];
            //var_dump(  $data);die;
            $res = Db::name('level')->insert($data);
            if ($res) {
                $this->success('添加成功', 'Level/index', 3);
            } else {
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }
    }
    //修改等级
    public  function editLevel(){
        $level_id=input('level_id');
        if ($this->request->isPost()) {

            $data = [
                'name'=>input('name'),
                'pricesum'=>input('pricesum'),
                'vipsum'=>input('vipsum'),
                'svipsum'=>input('svipsum'),
                't1'=>input('t1'),
                't2'=>input('t2'),
                'status'=>input('status'),
            ];
            //  var_dump($data);die;
            $res = Db::name('level')->where('level_id',
                $level_id)->update($data);
            if ($res) {
                $this->success('更新成功', 'Level/index', 3);
            } else {
                $this->error('更新失败');
            }
        }else{
            $list= Db::name('level')->where('level_id',$level_id)->find();
            $this->assign('list', $list);

            return $this->fetch();
        }

    }
    //删除等级
    public  function  delLevel(){
        $level_id=input('level_id');
        $list= Db::name('level')->where('level_id',$level_id)->find();
        if( empty($list)){
            $this->error('该等级不存在');
        }
        $res= Db::name('level')->where('level_id',$level_id)->delete();
        if(  $res){
            $this->success('删除成功','Level/index',3);
        }else{
            $this->error('删除失败，请联系管理员');
        }
    }


}