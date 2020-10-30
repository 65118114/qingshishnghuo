<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
class Notice extends Common{

    //通知首页
    public  function  index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('notice')->order('noti_id desc')->select();
        //  var_dump( $list);die;
        $this->assign('list', $list);
        return $this->fetch();

    }

    //添加通知
    public  function  addNotice(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($this->request->isPost()) {
            $img=input('img/a');
            $img=implode('|', $img);
            $data = [
                'title'=>input('title'),
                'content'=>input('content'),
                'img'=> $img,
                'create_time'=>time(),
            ];
            //var_dump(  $data);die;
            $res = Db::name('notice')->insert($data);
            if ($res) {
                $this->success('添加成功', 'Notice/index', 3);
            } else {
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }
    }
    //修改通知

    public  function  editNotice(){
        if ($this->request->isPost()) {
            $noti_id=input('noti_id');
            $list= Db::name('notice')->where('noti_id',$noti_id)->find();
            $img=input('img/a');
            if(empty($img)){
                $img=  $list['img'];
            }else{
                $img=input('img/a');

                $img=implode('|', $img);
            }
            $data = [
                'title'=>input('title'),
                'content'=>input('content'),
                'img'=> $img,
            ];
            //  var_dump($data);die;
            $res = Db::name('notice')->where('noti_id',
                $noti_id)->update($data);
            if ($res) {
                $this->success('更新成功', 'Notice/index', 3);
            } else {
                $this->error('更新失败');
            }
        }else{
            $noti_id=input('noti_id');
            $list= Db::name('notice')->where('noti_id',$noti_id)->find();
            $this->assign('list', $list);
            $img=explode('|',$list['img']);
            $this->assign('img',$img);
            //var_dump( $img);die;
            return $this->fetch();
        }

    }

    //删除
    public  function  delNotice(){
        $noti_id=input('noti_id');
        $list= Db::name('notice')->where('noti_id',$noti_id)->find();
        if(empty($list)){
            $this->error('该通知不存在');
        }
        $res= Db::name('notice')->where('noti_id',$noti_id)->delete();
        if(  $res){
            $this->success('删除成功','Notice/index',3);
        }else{
            $this->error('删除失败，请联系管理员');
        }
    }
    //详情
    public  function  detailNotice(){
        $noti_id=input('noti_id');
        $list= Db::name('notice')->where('noti_id',$noti_id)->find();
        //  var_dump($list);die;
        $this->assign('list',$list);
        return $this->fetch();
    }


    // 处理图片上传
    public  function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $files = $request->file();
        //  var_dump( $files);die;
        $validate=config('HEALTHY_VALIDATE');

        $path = config('NOTICE_PATH');
        foreach($files as $file ){
            $res=$file->validate($validate)->move($path);
            if($res){
                $file_path=$res->getPathname();

                return json(['status'=>'success','msg'=>config('server_name').'/'.$file_path]);
            }else{
                return json(['status'=>'fail','msg'=>$file->getError()]);
            }
        }
    }
}