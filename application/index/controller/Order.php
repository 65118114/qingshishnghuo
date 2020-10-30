<?php

namespace app\index\controller;

use think\Controller;
use think\db;
use think\Request;
use appwxpay\Wxpay;
use app\index\model\Order as orderModel;
use  alipay\aop\AopClient;

class Order extends Controller
{

    //订单入库
    public function addorder(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        // var_dump( $mobile)die;
        $userinfo = Db::name('user')->field('user_id,is_star,paypassword,money')->where('mobile', $mobile)->find();
        // $goods_id=input('goods_id');
        $address_id = input('address_id');
        // $trade_no=date('YmdHis').mt_rand(100000,999999);
        //如果有订单号 ,证明是二次付款需要把以前的订单的订单号更改

        if ($request->isPost()) {/*
            $goodslist=Db::name('order')->where('user_id',$user_id)->column('goods_id');
            if(in_array("100", $goodslist)){
                 return json(['gift'=>1]);
            }*/
            $goods_id = input('goods_id');
            $is_star = $userinfo['is_star'];
            if($is_star==1 &&  $goods_id==122){
                return json(['status' =>2, 'msg' => '已经是达人,请勿再次购买礼包']);
            }
            $data = [
                //  'user_id'=>input('user_id'),
                'user_id' => $userinfo['user_id'],
                'address_id' => $address_id,
                'goods_id' => input('goods_id'),
                'attr_id' => input('attr_id'),
                'num' => input('num'),
                'price' => input('price'),
                'pay_type' => input('pay_type'),
                'card_log_id' => input('card_log_id'),
                'goods_type' => input('goods_type'),
                'trade_no' => 'QSSH' . date('YmdHis') . mt_rand(10000, 99999),
                'create_time' => time(),
            ];
            if($data['num'] ==0){
                return json(['status' => 0, 'msg' => '数量不能为0']);
            }
            // if(!is_int($data['num'])){
            //     return json(['status' => 0, 'msg' => '数量不能为小数']);
            // }
            $is_on = Db::name('goods_attr')->where('attr_id', $data['attr_id'])->value('is_on');
            if($is_on ==0){
                return json(['status' => 0, 'msg' => '商品规格已经下架,请返回商品重新选招!']);
            }
            $order_id = input('order_id');
            if(empty($order_id)){
                $order_id = Db::name('order')->insert($data, 0, 1);
            }else{
                $trade_no = Db::name('order')->where('order_id', $order_id)->value('trade_no');
                $newtrade_no= 'QSSH'.substr( $trade_no,4).mt_rand(100,1000);
                Db::name('order')->where('order_id', $order_id)->update(['trade_no'=> $newtrade_no]);
            }
            $orderdata = Db::name('order')->where('order_id', $order_id)->find();
            $price=$orderdata['price'];
            if ($orderdata['pay_type'] == 1) {
                //微信支付
                $wxpay = new Wxpay();
                // var_dump($demo);die;
                $goods_name = Db::name('goods')->where('goods_id', $orderdata['goods_id'])->value('goods_name');
                 // 微信body名字最多是128个字符
                 if(strlen($goods_name) >=128){
                    $goods_name=substr($goods_name,0,30);
                }
                $response = $wxpay->getPrePayOrder($goods_name,$orderdata['trade_no'],1);
                //     $response['time']=time();
                if ($response['prepay_id']) {//判断返回参数中是否有prepay_id
                    $result = $wxpay->getOrder($response['prepay_id'], $response['nonce_str']);//执行二次签名返回参数
                }
                return json($result);
            } elseif ($orderdata['pay_type'] == 2) {
                //支付宝支付
                $goods_name = Db::name('goods')->where('goods_id', $data['goods_id'])->value('goods_name');
                $result = $this->zfbpay($goods_name, $orderdata['trade_no'], $orderdata['price']);
                return json(['content'=>$result]);
            } elseif ($orderdata['pay_type'] == 3) {
                //余额支付
                $oderOne = Db::name('order')->where('order_id', $order_id)->find();
                $userOne = Db::name('user')->where('user_id', $oderOne['user_id'])->find();
                $paypassword = input('paypassword');
                $paypassword=md5($paypassword);
                if ($paypassword != $userOne['paypassword']) {
                    return json(['status' => 0, 'msg' => '支付密码不正确']);
                } else {
                    $res = Db::name('user')->where('user_id', $oderOne['user_id'])->setDec('money', $oderOne['price']);
                    $orderres = Db::name('order')->where('order_id', $order_id)->update(['order_status' => 1, 'pay_time' => time()]);
                    if ($res && $orderres) {
                        //进行分润
                        $this->sharemoney($oderOne['user_id'], $oderOne['price'], $oderOne['goods_id'], $order_id);
                        return json(['status' => 1, 'msg' => '支付成功']);
                    } else {
                        return json(['status' => 0, 'msg' => '订单错误,请联系客服']);
                    }
                }
            }elseif ($orderdata['pay_type'] == 4) {
                $mobile = Db::name('user')->where('user_id', $orderdata['user_id'])->value('mobile');
                $userpaypassword = Db::name('user')->where('user_id', $orderdata['user_id'])->value('paypassword');
                $userstep = Db::name('user_step')->where('mobile', $mobile)->find();
                $attr_points = Db::name('goods_attr')->where('attr_id', $orderdata['attr_id'])->value('attr_points');
                  $paypassword = input('paypassword');
                $paypassword=md5($paypassword);
                if ($paypassword != $userpaypassword) {
                    return json(['status' => 0, 'msg' => '支付密码不正确']);
                }
               if( $attr_points>$userstep['power']){
                   return json(['status' => 0, 'msg' => '抵抗力不足']);
               }else{
                   $res = Db::name('user_step')->where('mobile', $mobile)->setDec('power',$attr_points);
                   $orderres = Db::name('order')->where('order_id', $order_id)->update(['order_status' => 1, 'pay_time' => time()]);
                   if ($res && $orderres) {
                       return json(['status' => 1, 'msg' => '支付成功']);
                   } else {
                       return json(['status' => 0, 'msg' => '订单错误,请联系客服']);
                   }
               }
            }
        }else {
            $where = [
                'user_id' =>$userinfo['user_id'],
                'status' => 1,
            ];
            $list = Db::name('address')->where('is_on', 1)->where($where)->find();
            if (empty($list)) {
                return json(array('status' => 0,'money'=>$userinfo['money'],'paypassword'=>$userinfo['paypassword']));
            } else {
                return json(array('status' => 1, 'msg' => $list,'money'=>$userinfo['money'],'paypassword'=>$userinfo['paypassword']));
            }
        }
    }

    //支付宝支付
    public function zfbpay($title, $trade_no, $total_fee)
    {
        // 引入支付宝第三方sdk类库
        // import('alipay.aop.AopClient', EXTEND_PATH);
        // 调用支付宝配置文件
        $API = config('ALIPAY_CONFIG');

        //构造业务请求参数的集合(订单信息)
        $content = array();
        $content['subject'] = $title;                 // 商品标题
        //   $content['body'] =$body;  // 商品描述 非必须
        $content['out_trade_no'] = $trade_no;         // 订单号
        $content['timeout_express'] = "15m";                         // 允许最晚的支付时间  非必须
        $content['total_amount'] = $total_fee;       // 要求必须为浮点型，订单金额
        $content['product_code'] = "QUICK_MSECURITY_PAY";            //销售产品码,固定值
        $con = json_encode($content);                                //$content是biz_content的值,将之转化成json字符串
        //公共参数
        $Client = new AopClient();                 //实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $param = array();
        $param['app_id'] = $API['app_id'];         //支付宝分配给开发者的应用ID
        $param['method'] = $API['method'];         //接口名称
        $param['charset'] = $API['charset'];       //请求使用的编码格式
        $param['sign_type'] = $API['sign_type'];   //商户生成签名字符串所使用的签名算法类型
        $param['timestamp'] = date("Y-m-d H:i:s");   //发送请求的时间
        $param['version'] = $API['version'];       //调用的接口版本，固定为：1.0
        $param['notify_url'] = $API['notify_url']; //支付宝服务器主动通知地址
        $param['biz_content'] = $con;              //业务请求参数的集合,长度不限,json格式
        $paramStr = $Client->getSignContent($param);//组装请求签名参数  到此步目前没有问题
        $sign = $Client->alonersaSign($paramStr, $API['rsaPrivateKey'], 'RSA2'); //生成签名
        $param['sign'] = $sign;
        $str = $Client->getSignContentUrlencode($param);       //最终请求参数
        return $str;
    }

    //回调
    public function wxnotify(Request $request)
    {

        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        //	echo '111';
        $xmlStr = file_get_contents('php://input');
      //  file_put_contents('wx3.txt', $xmlStr);
        $data = xmlToArray($xmlStr);
        $sign = $data['sign'];
        unset($data['sign']);
        // ksort($data);
        $wxpay = new Wxpay();
        $signStr = $wxpay->getSign($data);
        //  $signStr=http_build_query($data)."&key=".'c33367701511b4f6020ec61ded352059';
        // $newSign=strtoupper(hash_hmac("sha256",$signStr,'c33367701511b4f6020ec61ded352059'));
        if ($sign == $signStr) {
            $trade_no = $data['out_trade_no'];
            $list = Db::name('order')->where('trade_no', $trade_no)->find();
            $user_id = $list['user_id'];
            $price = $list['price'];
            $goods_id = $list['goods_id'];
            $order_id = $list['order_id'];
            $card_log_id= $list['card_log_id'];
            //达人商品不需要发货
              if($goods_id==122){
                  //更改订单状态
                  Db::name('order')->where('trade_no', $trade_no)->update(['order_status' => 4, 'pay_time' => time()]);
              }else{
                  //更改订单状态
                  Db::name('order')->where('trade_no', $trade_no)->update(['order_status' => 1, 'pay_time' => time()]);
              }

            //更改用户优惠券的状态
            if(!empty( $card_log_id)){
                Db::name('card_log')->where('card_log_id',$card_log_id)->update(['is_use' => 1]);
            }
            $is_star= Db::name('user')->where('user_id', $user_id)->value('is_star');
            if($is_star ==0 && $goods_id==122){
                Db::name('user')->where('user_id',$user_id)->update(['is_star' =>1]);
            }
            $this->sharemoney($user_id, $price, $goods_id, $order_id);
            return 'success';
        }
    }
//支付宝回调
    public function zfbnotify()
    {
        //$data=$request->param();
        //   $params =urldecode(file_get_contents('php://input')) ;
        //   file_put_contents('67.txt', $params);
        //      $API = config('ALIPAY_CONFIG');
        $Client = new AopClient();                 //实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $Client->postCharset="UTF-8";
        $Client->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhRdaZROrDuX9g/RDPr7NHgH7crmyWqrHOCEGfI6VUpIW/C5k4fikOjjXnF2uaql00J2oYd9NmJQPQMAiSp2Y3k6cJqu55oxznQoOfA5H/TjFvU0XgXHoYG28vCZjt5yc2jMKKQwDspNW7JRbM0Ot1jC2OGiLF+sA34VNSBrpEzpBQZ8xg8sZx5Agz/qIw21WXgJ8L2OnHOQHt9T7YnIMSP1vSNTQ/CdV+JJqZtMycdqIz4Pb72bD/IbxKkofwvyMD1fnCZBlykGJas8r+uevNoj5AyKt9N4qgJu9gdLRCKTkujSjYJwLrzvl7bJrJu0McEm4duVGERp8yc4BlXFJ4QIDAQAB';
        $alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhRdaZROrDuX9g/RDPr7NHgH7crmyWqrHOCEGfI6VUpIW/C5k4fikOjjXnF2uaql00J2oYd9NmJQPQMAiSp2Y3k6cJqu55oxznQoOfA5H/TjFvU0XgXHoYG28vCZjt5yc2jMKKQwDspNW7JRbM0Ot1jC2OGiLF+sA34VNSBrpEzpBQZ8xg8sZx5Agz/qIw21WXgJ8L2OnHOQHt9T7YnIMSP1vSNTQ/CdV+JJqZtMycdqIz4Pb72bD/IbxKkofwvyMD1fnCZBlykGJas8r+uevNoj5AyKt9N4qgJu9gdLRCKTkujSjYJwLrzvl7bJrJu0McEm4duVGERp8yc4BlXFJ4QIDAQAB';
        $flag = $Client->rsaCheckV1($_POST,$alipayrsaPublicKey, "RSA2");
        if ($flag)
        {
            if($_POST['trade_status']=='TRADE_SUCCESS'){
                $trade_no = $_POST['out_trade_no'];
                $list = Db::name('order')->where('trade_no', $trade_no)->find();
                $user_id = $list['user_id'];
                $price = $list['price'];
                $goods_id = $list['goods_id'];
                $order_id = $list['order_id'];
                $card_log_id= $list['card_log_id'];
                //达人商品不需要发货
                if($goods_id==122){
                    //更改订单状态
                    Db::name('order')->where('trade_no', $trade_no)->update(['order_status' =>4, 'pay_time' => time()]);
                }else{
                    //更改订单状态
                    Db::name('order')->where('trade_no', $trade_no)->update(['order_status' =>1, 'pay_time' => time()]);
                }
                //更改用户优惠券的状态
                if(!empty( $card_log_id)){
                    Db::name('card_log')->where('card_log_id',$card_log_id)->update(['is_use' => 1]);
                }
                $is_star= Db::name('user')->where('user_id', $user_id)->value('is_star');
             if($is_star==0 && $goods_id==122){
                    Db::name('user')->where('user_id',$user_id)->update(['is_star' =>1]);
                }
                $this->sharemoney($user_id, $price, $goods_id, $order_id);
                return 'success';

            }
        }else{
            return 'fail';
        }
    }
    //pc端电脑入库订单入库
    public function wapaddorder(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        //   var_dump($mobile);
        $user_id = Db::name('user')->where('mobile', $mobile)->value('user_id');
// var_dump(  $user_id);die;
        // $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        $address_id = input('address_id');
        // $trade_no=date('YmdHis').mt_rand(100000,999999);
        if ($request->isPost()) {
          $num= input('num');
          if($num<=0){
              return json(array('status' => 0, 'msg' => '数量不能为0'));
          }
            $data = [
                'user_id' => input('user_id'),
                // 'user_id'=> $user_id,
                'address_id' => $address_id,
                'goods_id' => input('goods_id'),
                'attr_id' => input('attr_id'),
                'num' =>$num,
                'price' => input('price'),
                'pay_type' => input('pay_type'),
                'trade_no' => 'QSSH' . date('YmdHis') . mt_rand(10000, 99999),
                'create_time' => time(),
            ];

            $order_id = Db::name('order')->insert($data, 0, 1);
            //$data=Db::name('order')->where('order_id', $order_id)->find();
            //var_dump($result);die;
            // $this->assign('result',$result);
            //return $this->fetch();
            return json(array('status' => 1, 'order_id' => $order_id, 'trade_no' => $data['trade_no']));
        } else {
            $where = [
                'user_id' => $user_id,
                'status' => 1,
            ];
            if (empty($address_id)) {
                $list = Db::name('address')->where('is_on', 1)->where($where)->find();
                return json($list);
            } else {
                $list = Db::name('address')->where('is_on', 1)->where('address_id', $address_id)->find();
                return json($list);
            }
        }

    }

    //支付
    public function payOrder($order_id)
    {
        // $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        // $user_id=Db::name('user')->where('mobile', $mobile)->value('user_id');
        //  $order_id= input('order_id');
        // $trade_no=date('YmdHis').mt_rand(100000,999999);
        $oderOne = Db::name('order')->where(array('order_id' => $order_id))->find();
        //  var_dump( $oderOne);die;

        $price = $oderOne['price'];
        $trade_no = $oderOne['trade_no'];

        // $result = $this->payorder( $trade_no, $price);

        require_once "../extend/wxpay/lib/WxPay.Api.php";
        require_once "../extend/wxpay/example/WxPay.JsApiPay.php";
        require_once "../extend/wxpay/example/WxPay.Config.php";
        require_once '../extend/wxpay/example/log.php';

        ///①、获取用户openid
        try {
            $tools = new \JsApiPay();
            $openId = $tools->GetOpenid();

            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("手机");
            $input->SetAttach("test");
            $input->SetOut_trade_no($trade_no);
            $input->SetTotal_fee($price * 100);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://api.qingshishenghuo.shop/index/Order/wxnotify");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $config = new \WxPayConfig();
            $order = \WxPayApi::unifiedOrder($config, $input);


            //  echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
            //printf_info($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            $result = array('jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress);
        } catch (Exception $e) {
            Log::ERROR(json_encode($e));
        }
        $result['order_id'] = $order_id;
        //var_dump(  $result);die;
        //$data=Db::name('order')->where('order_id', $order_id)->find();
        //var_dump($result);die;
        // $this->assign('result',$result);
       // return json($result);
        return $this->fetch();

    }



    /*   //支付
       public function payorder(  $trade_no, $price)
       {
           header("Access-Control-Allow-Origin:*");
           header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
           header('Access-Control-Allow-Headers:x-requested-with, content-type');

           require_once "../extend/wxpay/lib/WxPay.Api.php";
           require_once "../extend/wxpay/example/WxPay.JsApiPay.php";
           require_once "../extend/wxpay/example/WxPay.Config.php";
           require_once '../extend/wxpay/example/log.php';

           ///①、获取用户openid
           try {
               $tools = new \JsApiPay();
               $openId = $tools->GetOpenid();
               //②、统一下单
               $input = new \WxPayUnifiedOrder();
               $input->SetBody("手机");
               $input->SetAttach("test");
               $input->SetOut_trade_no($trade_no);
               $input->SetTotal_fee( $price);
               $input->SetTime_start(date("YmdHis"));
               $input->SetTime_expire(date("YmdHis", time() + 600));
               $input->SetGoods_tag("test");
               $input->SetNotify_url("http://shop.phtcsxy.com/index/Order/notify");
               $input->SetTrade_type("JSAPI");
               $input->SetOpenid($openId);
               $config = new \WxPayConfig();

               $order = \WxPayApi::unifiedOrder($config, $input);

               //  echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
               //printf_info($order);
               $jsApiParameters = $tools->GetJsApiParameters($order);
               //获取共享收货地址js函数参数
               $editAddress = $tools->GetEditAddressParameters();
               return array('jsApiParameters'=>$jsApiParameters,'editAddress'=>$editAddress);
           } catch (Exception $e) {
               Log::ERROR(json_encode($e));
           }
       }*/
    //回调
    public function notify()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        //	echo '111';
        $xmlStr = file_get_contents('php://input');
        //  file_put_contents('wx2.txt', $xmlStr);
        $data = xmlToArray($xmlStr);
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $signStr = http_build_query($data) . "&key=" . config('WXKEY');
        $newSign = strtoupper(hash_hmac("sha256", $signStr, config('WXKEY')));
        if ($sign == $newSign) {
            $trade_no = $data['out_trade_no'];
            $list = Db::name('order')->where('trade_no', $trade_no)->find();
            $user_id = $list['user_id'];
            $price = $list['price'];
            $goods_id = $list['goods_id'];
            $order_id = $list['order_id'];
            //给商品增加销量
            //达人商品不需要发货
            if($goods_id==122){
                //更改订单状态
                Db::name('order')->where('trade_no', $trade_no)->update(['order_status' =>4, 'pay_time' => time()]);
            }else{
                //更改订单状态
                Db::name('order')->where('trade_no', $trade_no)->update(['order_status' =>1, 'pay_time' => time()]);
            }
            $is_star= Db::name('user')->where('user_id', $user_id)->value('is_star');
            if($is_star ==0){
                Db::name('user')->where('user_id',$user_id)->update(['is_star' =>1]);
            }
            $this->sharemoney($user_id, $price, $goods_id, $order_id);
            return 'success';
        }
    }

    //订单支付成功进行分润
    protected function sharemoney($user_id, $price, $goods_id, $order_id)
    {
        $list = Db::name('user')->field('pid,ppid')->where('user_id', $user_id)->find();
        $res11 = Db::name('user')->where('user_id', $user_id)->setInc('pricesum', $price);
        $pid = $list['pid'];
        $ppid = $list['ppid'];
        if($goods_id ==122){
            //给商品增加销量 如果是达人商品 直接增加30个
            $res3 = Db::name('goods')->where('goods_id', $goods_id)->setInc('sale',30);

            if (!empty($pid)) {
                $goodsinfo = Db::name('goods')->where('goods_id', $goods_id)->find();
                $pidinfo = Db::name('user')->where('mobile', $pid)->find();
                $pidt1 = Db::name('level')->where('level_id', 3)->value('t1');
                //冻结金额
                $freeze = $price * $goodsinfo['goods_pv'] * $pidt1 / 10000;
                $data = [
                    'order_id' => $order_id,
                    'user_id' => $pidinfo['user_id'],
                    'freezemoney' => $freeze,
                    'freezeinfo' => '一级奖励',
                    'freezesign' => '+',
                    'freeze_time' => time(),
                    'unfreeze_time' => time(),
                    'status'=>1
                ];
                $data=[
                                        'order_id'=>$order_id,
                                        'user_id'=>$pidinfo['user_id'],
                                        'money'=>$freeze,
                                        'sign'=>'+',
                                        'create_time'=>time(),
                                        'info'=>'直接奖励',
                                    ];
                $res2=Db::name('user_money')->insert($data);

                $res = Db::name('user_freeze_log')->insert($data);
                //需要给用户增加余额
                $resss = Db::name('user')->where('mobile',$pid)->setInc('money', $freeze);
            }
            if (!empty($ppid)) {
                $goodsinfo = Db::name('goods')->where('goods_id', $goods_id)->find();
                $ppidinfo = Db::name('user')->where('mobile', $ppid)->find();
                $ppidt2 = Db::name('level')->where('level_id',3)->value('t2');

                //冻结金额
                $freeze2 = $price * $goodsinfo['goods_pv'] * $ppidt2 / 10000;
                //   var_dump(    $freeze);die;
                $data = [
                    'order_id' => $order_id,
                    'user_id' => $ppidinfo['user_id'],
                    'freezemoney' => $freeze,
                    'freezeinfo' => '二级奖励',
                    'freezesign' => '+',
                    'freeze_time' => time(),
                    'unfreeze_time' => time(),
                    'status'=>1
                ];
                $res = Db::name('user_freeze_log')->insert($data);
                          $data=[
                                        'order_id'=>$order_id,
                                        'user_id'=>$ppidinfo['user_id'],
                                        'money'=> $freeze2,
                                        'sign'=>'+',
                                        'create_time'=>time(),
                                        'info'=>'间接奖励',
                                    ];
                $res2=Db::name('user_money')->insert($data);


                $resss = Db::name('user')->where('mobile',$ppid)->setInc('money', $freeze);
            }
        }else{
            $res3 = Db::name('goods')->where('goods_id', $goods_id)->setInc('sale',1);
            if (!empty($pid)) {
                $goodsinfo = Db::name('goods')->where('goods_id', $goods_id)->find();
                $pidinfo = Db::name('user')->where('mobile', $pid)->find();
                //if($pidinfo['level'] ==0){
                //	return false;
                //}
                //  $pidt1 = Db::name('level')->where('level_id', $pidinfo['level'])->value('t1');
                $pidt1 = Db::name('level')->where('level_id', 3)->value('t1');

                //冻结金额
                $freeze = $price * $goodsinfo['goods_pv'] * $pidt1 / 10000;

                $data = [
                    'order_id' => $order_id,
                    'user_id' => $pidinfo['user_id'],
                    'freezemoney' => $freeze,
                    'freezeinfo' => '一级奖励',
                    'freezesign' => '+',
                    'freeze_time' => time(),
                ];

                $res = Db::name('user_freeze_log')->insert($data);
            }
            if (!empty($ppid)) {
                $goodsinfo = Db::name('goods')->where('goods_id', $goods_id)->find();
                $ppidinfo = Db::name('user')->where('mobile', $ppid)->find();
                $ppidt2 = Db::name('level')->where('level_id',3)->value('t2');

                //冻结金额
                $freeze = $price * $goodsinfo['goods_pv'] * $ppidt2 / 10000;
                //   var_dump(    $freeze);die;
                $data = [
                    'order_id' => $order_id,
                    'user_id' => $ppidinfo['user_id'],
                    'freezemoney' => $freeze,
                    'freezeinfo' => '二级奖励',
                    'freezesign' => '+',
                    'freeze_time' => time(),
                ];
                $res = Db::name('user_freeze_log')->insert($data);
            }
        }

    }

    //异步通知
    public function checkstatus(Request $request)
    {
        //  echo  111;
        $order_id = $request->param('order_id');
        //  var_dump($order_id);die;
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $status = Db::name('order')->where('order_id', $order_id)->value('order_status');
            if ($status == 1) {
                return json(['status' => 1]);
            } else {
                return json(['status' => 0]);
            }
        }
    }
    //跳转
    public function paysuccess()
    {
        $order_id = input('order_id');
        var_dump($order_id);
        die;
        //  echo '支付成功';
    }


}