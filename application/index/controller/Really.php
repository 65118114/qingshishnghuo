<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Route;
use think\validate;

class Really extends Controller{
    //添加身份信息
    public function addReally(Request $request){
		header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $data=[
                'name' => input('name'),
                'card' => input('card'),
                'pic1' => input('pic1'),
                'pic2' => input('pic2'),
                'pic3' => input('pic3'),
                'create_time'=>time(),
                'realstatus'=>2,
            ];
            $mobile= input('mobile');
            $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
            //var_dump( $data);die;
            $data['user_id']= $user_id;
          $res=Db::name('really')->insert($data);
            if( $res){
              //  Db::name('really_id',$really_id)->update(['realstatus'=>1]);
                //更新数据库用户得状态
                $result=Db::name('user')->where('mobile', $mobile)->update(['is_real'=>2]);
                return json(['status'=>1,'info'=>'提交成功,等待系统审核!']);
            }else{
                return json(['status'=>0,'info'=>'网络错误!']);
            }
        }
       
    }
  //首页
    public function index(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile= input('mobile');
        $user_id=Db::name('user')->where('mobile',$mobile)->value('user_id');
        $list=Db::name('really')->where('user_id',$user_id)->order('really_id desc')->find();
      //  var_dump( $list);die;
        if(!empty( $list)) {
            $list['newcard'] = sfzcard($list['card']);
        }
        return json( $list);
    }

    // 处理图片上传
    public  function upload1(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $file = $request->file('pic1');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('REALLY_PATH');
           // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res=$file->validate($validate)->move($path);
            if($res){
                //var_dump(    $res);die;
                $file_path=$res->getPathname();
                return json(['status'=>1,'info'=>config('server_name').'/'.$file_path]);
            }else{
                return json(['status'=>0,'info'=>$file->getError()]);
            }
        }

    }
    // 处理图片上传
    public  function upload2(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $file = $request->file('pic2');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('REALLY_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res=$file->validate($validate)->move($path);
            if($res){
                //var_dump(    $res);die;
                $file_path=$res->getPathname();
                return json(['status'=>1,'info'=>config('server_name').'/'.$file_path]);
            }else{
                return json(['status'=>0,'info'=>$file->getError()]);
            }
        }

    }
    // 处理图片上传
    public  function upload3(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $file = $request->file('pic3');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('REALLY_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res=$file->validate($validate)->move($path);
            if($res){
                //var_dump(    $res);die;
                $file_path=$res->getPathname();
                return json(['status'=>1,'info'=>config('server_name').'/'.$file_path]);
            }else{
                return json(['status'=>0,'info'=>$file->getError()]);
            }
        }

    }
}