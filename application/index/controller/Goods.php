<?php
namespace app\index\controller;
use think\db;
use think\facade\Cookie;
use think\Request;
use think\db\Where;
class Goods
{
    //商品首页
    public function index()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $goods_type = input('goods_type');
        $page = input('page');
        $mobile = input('mobile');
        if($goods_type > -1){
            $where['goods_type']= $goods_type;
        }
        //goods_type 1 为实体商品  2是抵抗力兑换商品 3 监控商品
        if($goods_type ==2){
            $goods = Db::name('goods')->where($where)->where('is_on',1)->field('goods_rule,goods_id,goods_name,sale')->select();
            //echo "<pre>";
            foreach ($goods as $k => $v) {
                //   var_dump($v['goods_id']);
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->where('is_on',1)->field('attr_id,attr_specs,attr_points')->where(array('goods_id' => $v['goods_id']))->find();
                //  var_dump( $goods_gallery_one);
                $video_url= Db::name('goods_video')->where(array('goods_id' => $v['goods_id']))->value('url');
                $goods_content= Db::name('goods_content')->where(array('goods_id' => $v['goods_id']))->value('goods_content');
                $goods[$k]['video_url']=$video_url;
                $goods[$k]['goods_content']= $goods_content;
                $goods[$k] = array_merge($goods[$k], $goods_gallery_one,$goods_attr);
            }
            $last_ages = array_column($goods,'attr_points');
            array_multisort($last_ages ,SORT_ASC,$goods);
        }elseif($goods_type ==1){
             
              if(empty($page)){
                    $goods = Db::name('goods')->where($where)->where('is_on',1)->field('goods_id,goods_name,sale')->order('pro_price asc')->select();
            //echo "<pre>";
            foreach ($goods as $k => $v) {
                //   var_dump($v['goods_id']);
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->field('ppro_price')->order('ppro_price asc')->where(array('goods_id' => $v['goods_id']))->where('is_on',1)->find();
                //  var_dump( $goods_gallery_one);
                if(!empty($goods_attr)){
                    $goods[$k] = array_merge($goods[$k], $goods_gallery_one,$goods_attr);
                }else{
                    $goods[$k] = array_merge($goods[$k], $goods_gallery_one);
                }

            }
           } else{
             $size=10;
                 $goods = Db::name('goods')->where($where)->limit(($page-1)* $size, $size)->where('is_on',1)->field('goods_id,goods_name,sale')->order('pro_price asc')->select();
            //echo "<pre>";
            foreach ($goods as $k => $v) {
                //   var_dump($v['goods_id']);
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->field('ppro_price')->order('ppro_price asc')->where(array('goods_id' => $v['goods_id']))->where('is_on',1)->find();
                //  var_dump( $goods_gallery_one);
                if(!empty($goods_attr)){
                    $goods[$k] = array_merge($goods[$k], $goods_gallery_one,$goods_attr);
                }else{
                    $goods[$k] = array_merge($goods[$k], $goods_gallery_one);
                }

            }
           }


        
        }elseif($goods_type ==3){
             $goods= Db::name('goods_video')->where('video_type',1)->select();
             $is_star= Db::name('user')->where('mobile',$mobile)->value('is_star');
            foreach ($goods as $k => $v) {
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_attr = Db::name('goods_attr')->field('ppro_price,ooriginal_price')->where(array('goods_id' => $v['goods_id']))->find();
                $goods_info = Db::name('goods')->where(array('goods_id' => $v['goods_id']))->field('goods_id,goods_name')->find();
                $goods_content= Db::name('goods_content')->where(array('goods_id' => $v['goods_id']))->value('goods_content');
                $goods[$k]['goods_content']= $goods_content;
                $goods[$k]['is_star']= $is_star;
                $goods[$k] = array_merge($goods[$k], $goods_gallery_one,$goods_info,$goods_attr );
            }
        }
        return json($goods);
    }
    //安卓ios端商品
    public function bothdetail( )
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $goods_id = input('goods_id');
        $mobile = input('mobile');
        $level=Db::name('goods_video')->where('goods_id',$goods_id)->value('url');
        $is_star=Db::name('user')->where('mobile',$mobile)->value('is_star');
        // var_dump(  $goods_id);die;
        $goods = Db::name('goods')->field('goods_rule',true)->where(array('goods_id' => $goods_id))->find();
        
   //   var_dump( $goods);die;
       if(empty($level)){
              $goods['level']= '';
       }else{
          $goods['level']= $level;
       }
        if(empty($is_star)){
            $goods['is_star']= 0;
        }else{
            $goods['is_star']= $is_star;
        }
/*        $cardnum=Db::name('card')->where('card_num','<>',0)->count('card_id');
        if($cardnum>0){
            $goods['is_havecard']= 1;
        }else{
            $goods['is_havecard']= 0;
        }*/
        $goods_gallery = Db::name('goods_gallery')->field('gallery_id,goods_id',true)->where(array('goods_id' => $goods_id))->find();
       $goods_gallery=array_filter( $goods_gallery);
     ///  var_dump($goods_gallery);die;
        $goods_parm = Db::name('goods_parm')->where(array('goods_id' => $goods_id))->select();
        //$goods_parm=json_encode($goods_parm);
        // $goods_parm= json_encode((object)$goods_parm );
        //  var_dump( $goods_parm);die;
    //    $goods_attr = Db::name('goods_attr')->where(array('goods_id' => $goods_id))->order('attr_id asc')->select();
     //   $goods_attr= json_encode((object)$goods_attr );
        $goods_content = Db::name('goods_content')->where(array('goods_id' => $goods_id))->value('goods_content');
     //  $goods_content=htmlspecialchars_decode( $goods_content);
    //  $goods_content= strip_tags( $goods_content,'<img>');
    //  $goods_content=explode("<img ",$goods_content);
      // $goods_content=   array_shift($goods_content);
     //  $goods_content=  array_splice($goods_content,1);
      //$goods_content=implode('',$goods_content);
       // $goods_content=stripHtmlTags(['<p>'],$goods_content,true);
   // var_dump($goods_content);die;
        //  $list=array_merge();
         // return json($goods_content);
        $goods['goods_content']=  $goods_content;
        $goods['image']=  $goods_gallery;
       // if(!empty($goods_gallery)){
      //     $goods=array_merge($goods,$goods_gallery);
     //  }
//        $arr=[];
//        $arr[0]=$goods_gallery['goods_gallery_1'];
//        $arr[1]=$goods_gallery['goods_gallery_2'];
//        $arr[2]=$goods_gallery['goods_gallery_3'];
//        $arr[3]=$goods_gallery['goods_gallery_4'];
//        $arr[4]=$goods_gallery['goods_gallery_5'];
     //return json(array_values($goods,'image'=>array_filter($arr)]));
       return json($goods);
    }
    //安卓和ios 规格
    public function attrdetail( )
    {

        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        // echo '11';
        $goods_id = input('goods_id');
        $goods_parm = Db::name('goods_parm')->where(array('goods_id' => $goods_id))->select();
            $goods_attr = Db::name('goods_attr')->field('is_on',true)->where('is_on',1)->where(array('goods_id' => $goods_id))->order('ppro_price asc')->select();
        //$goods_content = Db::name('goods_content')->where(array('goods_id' => $goods_id))->find();
        return json(array($goods_parm,$goods_attr));
    }

    //pc端商品详情
    public function detail( )
    {

        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        // echo '11';
        $goods_id = input('goods_id');
        $mobile = input('mobile');
        $userlevel=Db::name('user')->field('is_star,level,head')->where('mobile',$mobile)->find();
        // var_dump(  $goods_id);die;
        $goods = Db::name('goods')->where(array('goods_id' => $goods_id))->find();
        $goods_gallery = Db::name('goods_gallery')->where(array('goods_id' => $goods_id))->find();
        $goods_parm = Db::name('goods_parm')->where(array('goods_id' => $goods_id))->select();
        $goods_attr = Db::name('goods_attr')->where('is_on',1)->where(array('goods_id' => $goods_id))->order('attr_id asc')->select();
        $goods_content = Db::name('goods_content')->where(array('goods_id' => $goods_id))->select();
        return json(array($goods, $goods_gallery, $goods_parm, $goods_attr, $goods_content, $userlevel));
    }
    //购物车首页
    public function cart()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:get'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile=input('mobile');
        $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        $is_star=Db::name('user')->where('mobile', $mobile)->value('is_star');
        $appraisedata=Db::name('user_appraise')->where('user_id',$user_id)->select();
        //var_dump($appraisedata);die;
        foreach ( $appraisedata as $k => $v) {
           // var_dump( $appraisedata[$k]);
            $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
            $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' => $v['goods_id']))->find();
            $goods_attr = Db::name('goods_attr')->field('attr_stock,attr_specs,ppro_price,ooriginal_price')->where(array('attr_id' => $v['attr_id']))->find();
            if(!empty( $goods_attr)){
                $appraisedata[$k] = array_merge(  $appraisedata[$k] ,$goods_gallery_one,$goods_name, $goods_attr );
            }else{
                $appraisedata[$k] = array_merge(  $appraisedata[$k] ,$goods_gallery_one,$goods_name);
            }
        $appraisedata[$k]['is_star']=$is_star;
        }
        //var_dump($goods_attr);
        return json( $appraisedata);


      //var_dump(Cookie::has('goods_list'));die;
       /* if (Cookie::has('goods_list')) {
            $goods_list = explode('===', Cookie::get('goods_list'));
            //var_dump(   $goods_list);die;
            $goods_cart = [];
            foreach ($goods_list as $k => $v) {
                $v = json_decode($v,true);
                $goods_info = Db::name('goods')->where('goods_id',$v['goods_id'])->find();
                $goods_attr = Db::name('goods_attr')->where('attr_id',$v['attr_id'])->find();
                $goods_gallery_one = Db::name('goods_gallery')->where('goods_id',$v['goods_id'])->field('goods_gallery_1')->find();
                $goods_cart[$k]['goods_name'] = $goods_info['goods_name'];
                $goods_cart[$k]['goods_gallery_one'] =  $goods_gallery_one;
                $goods_cart[$k]['goods_attr_specs'] =   $goods_attr['attr_specs'];
                $goods_cart[$k]['ppro_price'] =   $goods_attr['ppro_price'];
                $goods_cart[$k]['goods_num'] = $v['goods_num'];
                $goods_cart[$k]['cart_id'] = $k;
            }
           // var_dump( $goods_cart);die;
              return json( $goods_cart);
        }else{
            return json(['status'=>0]);
        }*/
    }
    //购物车下面得推荐
    public   function  likecart(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $list=Db::name('goods')->where(rand())->where('goods_id','<>','100')->find();
        $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $list['goods_id']))->find();
        $goods_name = Db::name('goods')->field('goods_name')->where(array('goods_id' => $list['goods_id']))->find();
        $goods_attr = Db::name('goods_attr')->field('ppro_price')->where(array('goods_id' => $list['goods_id']))->find();
        $list=array_merge( $list,$goods_gallery_one,$goods_name,$goods_attr);
        return json($list);
    }
    //添加购物车
    public function addCart()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile=input('mobile');
        $user_id=Db::name('user')->where('mobile',$mobile)->value('user_id');
       // $userdata=Db::name('user_appraise')->where('user_id', $user_id)->select();
       //var_dump(  $userdata);die;
        $data=[
            'user_id'=> $user_id,
            'goods_id'=>input('goods_id'),
            'attr_id'=>input('attr_id'),
            'goods_num'=>input('goods_num'),
        ];
       $where=[
           'goods_id'=> $data['goods_id'],
           'attr_id'=> $data['attr_id'],
           'user_id'=> $user_id,
       ];
        $res=Db::name('user_appraise')->where($where)->find();
        if( $res){
            $incnum=  $res['goods_num']+ $data['goods_num'];
            $result=Db::name('user_appraise')->where('cart_id',$res['cart_id'])->update(['goods_num'=> $incnum]);
            if( $result){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }else{
           // echo 111;
            $result=Db::name('user_appraise')->insert( $data);
            if(  $result){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }

/*        foreach ( $userdata as $v){

            if( $v['goods_id'] == $data['goods_id'] &&  $v['attr_id'] == $data['attr_id']){
                $incnum=  $v['goods_num']+  $data['goods_num'];
                $res=Db::name('user_appraise')->where('cart_id', $userdata['cart_id'])->update(['goods_num'=> $incnum]);
            }else{
                $res=Db::name('user_appraise')->insert(  $data);
                //   var_dump(  111);die;
            }
        }*/


    //下面是cookie实现购物车

       /* $data = $request->param();
       // var_dump($data);die;
      // var_dump( Cookie('goods_list'));die;
        if (Cookie::has('goods_list')) {
            $goods_list = explode('===', Cookie::get('goods_list'));
            foreach ($goods_list as $k => $v) {
                $v = json_decode($v, true);
                if ($v['goods_id'] == $data['goods_id'] && $v['attr_id'] == $data['attr_id'] ) {
                    $v['goods_num'] = $v['goods_num'] + $data['goods_num'];
                    $goods_list[$k] = json_encode($v);
                    $on = true;
                }
            }
            $goods_list = implode('===', $goods_list);
            if (isset($on)) {
                Cookie::set('goods_list', $goods_list);
                return json(['status'=>1]);
            } else {
                Cookie::set('goods_list', $goods_list . '===' . json_encode($data));
                return json(['status'=>1]);
            }
        } else {
            Cookie::set('goods_list', json_encode($data));
            return json(['status'=>1]);
        }*/

    }
    //删除购物车
    public function delCart(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $cart_id = input('cart_id/a');
        //  var_dump( $cart_id);die;
            foreach ( $cart_id  as $k=> $v){
                $res=Db::name('user_appraise')->where('cart_id',$v)->delete();
            }
            if( $res ){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }

    //修改购物车
    public function editCart(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if($request->isPost()){
            $cart_id = input('cart_id');
        //  var_dump( $cart_id);die;
            $goods_num   = input('goods_num');
                 $res=Db::name('user_appraise')->where('cart_id',$cart_id)->update(['goods_num'=>$goods_num]);
            if( $res ){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }

       // Cookie::clear('goods_list');
//        $cart_id = $request->param('cart_id');
//
//        $goods_list = explode('===', Cookie::get('goods_list'));
//        unset($goods_list[ $cart_id]);
//        $goods_list = implode('===', $goods_list);
//
//       $res= Cookie::set('goods_list', $goods_list);
//        if(  $res ){
//            return json(['status'=>1]);
//        }else{
//            return json(['status'=>0]);
//        }
    }
    public  function  goodsvidoe(){
        $video_id = input('video_id');
      //  var_dump($video_id);die;
        $res= Db::name('goods_video')->where('video_id',$video_id)->setInc('visit_num',1);

        $goods_id = input('goods_id');
        $url= Db::name('goods_video')->where('goods_id', $goods_id)->field('url')->find();
        return json($url);
    }
}