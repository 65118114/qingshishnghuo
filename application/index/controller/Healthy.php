<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;
class Healthy extends Controller{
   //添加健康圈
    public function addhealthy(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
            if ($request->isPost()) {
                $file = input('image');
                $newfile = substr($file, 0, -3);
                $mobile = input('mobile');
                $userinfo = Db::name('user')->field('user_id,head')->where('mobile', $mobile)->find();
                if(empty($userinfo['head'])){
                    $head='http://api.qingshishenghuo.shop/uploads/healthy/20200402/42cd6a37c298a8432038725220c24f95.png';
                }else{
                    $head=$userinfo['head'];
                }
                $data = [
                    'user_id' => $userinfo['user_id'],
                    'content' => input('content'),
                    'create_time' => time(),
                    'img' => $newfile,
                    'goods_id'=>input('goods_id'),
                    'mobile' => input('mobile'),
                    'head'=> $head,
                    'sharenum'=> mt_rand(10,200),
                    'is_image'=>input('is_image'),
                ];
           //    var_dump($data);die;
                $res=Db::name('healthy')->insert($data);
                if($res){
                    return json(['status'=>1]);
                }else{
                    return json(['status'=>0]);
                }

            } else {
                $good_list=Db::name('goods')->where('goods_type',1)->where('goods_id','<>',100)->select();
                foreach ($good_list as $k => $v) {
                    // var_dump( $appraisedata[$k]);
                    $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                    $goods_attr = Db::name('goods_attr')->field('ooriginal_price,attr_specs,ppro_price')->where(array('goods_id' => $v['goods_id']))->find();
                 //   var_dump( $goods_attr);die;
                    if(!empty( $goods_gallery_one) && !empty( $goods_attr)){
                        $good_list[$k] = array_merge( $good_list[$k] ,$goods_gallery_one,$goods_attr);
                    }
                }
                return json( $good_list);
            }
    }
    //展示健康圈
    public function showhealthy(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
         $mobile = input('mobile');
        $user_id = Db::name('user')->where(array('mobile' => $mobile))->value('user_id');
        $heal_id = Db::name('user_hate')->field('heal_id')->where(array('user_id' =>$user_id))->select();
     //   $heal_id=array_values($heal_id);
     //  $len=count($heal_id )-1;
         $page = input('page');
           $size=10;
       foreach ($heal_id as $k=>$v){
           $a.=$v['heal_id'].',';
       }
       //var_dump($a);
       $a=array_filter(explode(',',$a));
        if(empty($page)){
           $list = Db::name('healthy')
            ->where('status',1)
            ->order('heal_id desc')
           -> whereNotIn('heal_id',$a)
            ->select();
       }else{
          $list = Db::name('healthy')
            ->where('status',1)
            ->order('heal_id desc')
           -> whereNotIn('heal_id',$a)
            -> limit(($page-1)* $size, $size)
            ->select();
       }
        //var_dump( $list);die;
        foreach ($list as $k => $v) {
          if(!empty($v['img'])){
                $list[$k]['image'] = explode('===', $v['img']);
        }
            $nickname = Db::name('user')->where(array('mobile' => $v['mobile']))->value('nickname');
            $list[$k]['nickname'] =  $nickname;
            if(!empty($v['goods_id'])){
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->order('attr_id')->field('ooriginal_price,attr_specs,ppro_price')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' => $v['goods_id']))->find();
                //   var_dump( $goods_attr);die;
                $list[$k] = array_merge( $list[$k],$goods_gallery_one,$goods_attr, $goods_name);
            }
            $comment_list=Db::name('comment')->where('heal_id',$v['heal_id'])->select();

           if(!empty($comment_list)){
               $list[$k]['comment'] = $comment_list;
           }
        }
       return json($list);
    }
    //精选
    public function choiceheal(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $user_id = Db::name('user')->where(array('mobile' => $mobile))->value('user_id');
        $heal_id = Db::name('user_hate')->field('heal_id')->where(array('user_id' =>$user_id))->select();
        //   $heal_id=array_values($heal_id);
        //  $len=count($heal_id )-1;
        foreach ($heal_id as $k=>$v){
            $a.=$v['heal_id'].',';
        }
        $list = Db::name('healthy')
            ->where('status',1)
            ->where('is_choice',1)
            ->order('heal_id desc')
            -> whereNotIn('heal_id',$a)
            ->select();
        foreach ($list as $k => $v) {
          if(!empty($v['img'])){
                $list[$k]['image'] = explode('===', $v['img']);
            }
            $nickname = Db::name('user')->where(array('mobile' => $v['mobile']))->value('nickname');
            $list[$k]['nickname'] =  $nickname;
            $commentnum=Db::name('comment')->where('heal_id',$v['heal_id'])->count('comment_id');
            $list[$k]['commentnum']= $commentnum;
            //   $list[$k]['share'] = mt_rand(10,200);
            if(!empty($v['goods_id'])){
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->order('attr_id ')->field('ooriginal_price,attr_specs,ppro_price')->where(array('goods_id' => $v['goods_id']))->find();
                //   var_dump( $goods_attr);die;
                $list[$k] = array_merge( $list[$k],$goods_gallery_one,$goods_attr);
            }
            $comment_list=Db::name('comment')->where('heal_id',$v['heal_id'])->select();

            if(!empty($comment_list)){
                $list[$k]['comment'] = $comment_list;
            }
            //var_dump($list);
        }
        return json($list);
    }
    //健康圈详情
    public  function  healdetail(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $heal_id=input('heal_id');
        $user_id=input('user_id');
        $userinfo = Db::name('user')->field('nickname,head')->where(array('user_id' => $user_id))->find();
        $list = Db::name('healthy')->where('heal_id',$heal_id)->find();
        if(!empty($list['goods_id'])){
            $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' => $list['goods_id']))->value('goods_name');
            $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $list['goods_id']))->value('goods_gallery_1');
            $goods_attr = Db::name('goods_attr')->order('attr_id ')->field('ooriginal_price,attr_specs,ppro_price')->where(array('goods_id' => $list['goods_id']))->find();
        }
       // $makehead = Db::name('user')->field('head')->where(array('mobile' => $list['makemobile']))->find();
        $list['nickname'] =  $userinfo['nickname'];
        $list['head'] =  $userinfo['head'];
       // $list['makehead'] =  $makehead['head'];
        $list['goods_name'] = $goods_name;
        $list['goods_gallery_one'] = $goods_gallery_one;
        $list['ppro_price'] =  $goods_attr['ppro_price'];
        $list['attr_specs'] =  $goods_attr['attr_specs'];
        if(!empty( $list['img'])){
            $list['image'] = explode('===',$list['img']);
        }
        $comment_list=Db::name('comment')->where('heal_id',$heal_id)->select();

        if(!empty($comment_list)) {
            foreach ($comment_list as $k => $v) {
                if (empty($v['makehead'])) {
                    $head = Db::name('user')->where('mobile',$v['makemobile'])->value('head');
                    $comment_list[$k]['makehead'] =  $head;
                }
         //    var_dump($v['makehead'] );die;
                if (empty($v['makenickname'])) {
                    $makenickname = Db::name('user')->where('mobile', $v['makemobile'])->value('nickname');
                    $comment_list[$k]['makenickname'] = $makenickname;
                }
            }
        }
        if(!empty($comment_list)){
            $list['comment']=$comment_list;
        }
        return json($list);
    }
    //历史发布
    public  function  hisheal(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile=input('mobile');
        $userinfo=Db::name('user')->field('user_id,nickname')->where('mobile', $mobile)->find();
        $list=Db::name('healthy')->order('heal_id desc')->where('user_id',$userinfo['user_id'])->select();
        foreach ($list as $k=>$v){
            if(!empty($v['img'])){
                $list[$k]['newimg']=explode('===',$v['img']);
            }
            $list[$k]['nickname']=$userinfo['nickname'];
             if(!empty($v['goods_id'])){
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->order('attr_id ')->field('ooriginal_price,attr_specs,ppro_price')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' => $v['goods_id']))->find();
                //   var_dump( $goods_attr);die;
                $list[$k] = array_merge( $list[$k],$goods_gallery_one,$goods_attr, $goods_name);
            }
            $comment_list=Db::name('comment')->where('heal_id',$v['heal_id'])->select();
            if(!empty($comment_list)){
                $list[$k]['comment'] = $comment_list;
            }
        }
        return json($list);
    }
    //随机视频
    public  function  randvideo(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $videolist=Db::name('healthy')->field('heal_id,mobile,img,num,head,content')->where('is_image',0)->select();
        foreach ( $videolist as $k=>$v){
            $userinfo=Db::name('user')->field('user_id,nickname')->where('mobile', $v['mobile'])->find();
            $commentnum=Db::name('comment')->where('heal_id',$v['heal_id'])->count('comment_id');
            $videolist[$k]['nickname']=$userinfo['nickname'];
            $videolist[$k]['user_id']=$userinfo['user_id'];
            $videolist[$k]['commentnum']= $commentnum;
        }
        return json( $videolist);
    }
    //删除健康圈
    public  function delheal(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $heal_id=input('heal_id');
            $res=Db::name('healthy')->where('heal_id',$heal_id)->delete();
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
    //点击增加点赞数量
    public  function  addnum(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $heal_id=input('heal_id');
        if($request->isPost()){
            $res=Db::name('healthy')->where('heal_id',$heal_id)->setInc('num',1);
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
    //分享次数
    public  function  sharenum(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $heal_id=input('heal_id');
        if($request->isPost()){
            $res=Db::name('healthy')->where('heal_id',$heal_id)->setInc('sharenum',1);
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
    //添加评论
    public function addcomment(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            //  makemobile是评论人的手机号
            $makemobile= input('makemobile');
            $heal_id=input('heal_id');
            $userinfo=Db::name('healthy')->field('user_id,mobile')->where('heal_id',$heal_id)->find();
            $nickname=Db::name('user')->where('mobile',$userinfo['mobile'])->value('nickname');
            $makenickname=Db::name('user')->where('mobile',$makemobile)->value('nickname');
            $makehead=Db::name('user')->where('mobile',$makemobile)->value('head');
           // $user_id=Db::name('user')->where('mobile',$mobile)->value('user_id');
            //接受评论
            $data=[
                'heal_id'=>$heal_id,
                'content'=>input('content'),
                'user_id'=> $userinfo['user_id'],
                'mobile'=> $userinfo['mobile'],
                'create_time'=>time(),
                'makemobile'=> $makemobile,
                'nickname'=> $nickname,
                'makenickname'=> $makenickname,
                'makehead'=> $makehead,
            ];
            $res=Db::name('comment')->insert($data);
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
    //回复评论
    public  function  answercomm(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $comment_id=input('comment_id');
            $answer_content=input('answer_content');
            $res=Db::name('comment')->where('comment_id',$comment_id)->update(['answer_content'=> $answer_content]);
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
    //屏蔽健康圈
    public  function  hatehealthy(Request $request){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile=input('mobile');
        $user_id = Db::name('user')->where(array('mobile' =>$mobile))->value('user_id');
        if($request->isPost()){
             $data=[
                  'user_id'=>$user_id,
                  'heal_id'=>input('heal_id'),
             ];
            $res=Db::name('user_hate')->insert($data);
            if($res){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }
/*    // 处理图片上传
    public  function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $file = $request->file('file');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('HEAD_PATH');
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

    }*/
//健康圈视频

    // 处理图片上传
    public function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $files = $request->file('file');
        // var_dump( $files);die;
        $validate = config('VALIDATE');
        $path = config('ASSESS_PATH');
    /*  foreach ($files as $file) {
            $res = $file->validate($validate)->move($path);
            if ($res) {
                $file_path .=config('server_name').'/'.$res->getPathname() . '===';
                // return json(['status' => 1, 'info' => 'http://qingshi.natapp1.cc/' . '/' . $file_path]);
            } else {
               return json(['status'=>0,'msg'=>$file->getError()]);
            }
        }
        return $file_path;*/
     if($request->isPost()){
            $file = $request->file('file');
            //  var_dump( $file);die;
            $validate=config('VALIDATE');
            $path = config('ASSESS_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
            // var_dump($file->validate($validate));die;
            $res=$file->validate($validate)->move($path);
          //  var_dump(1);die;
            if($res){
                //var_dump(    $res);die;
                $file_path=$res->getPathname();
                return json(['status'=>1,'info'=>config('server_name').'/'.$file_path.'===']);
            }else{
                return json(['status'=>0,'info'=>$file->getError()]);
            }
        }
    }
    // 处理图片上传
    public function pcupload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $files = $request->file('file');
        // var_dump( $files);die;
        $validate = config('VALIDATE');
        $path = config('ASSESS_PATH');
        foreach ($files as $file) {
            $res = $file->validate($validate)->move($path);
            if ($res) {
                $file_path .=config('server_name').'/'.$res->getPathname() . '===';
                // return json(['status' => 1, 'info' => 'http://qingshi.natapp1.cc/' . '/' . $file_path]);
            } else {
                //  return json(['status'=>'fail','msg'=>$file->getError()]);
            }
        }
        return $file_path;
    }

}