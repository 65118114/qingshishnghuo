<?php

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\validate;
use \DateTime;

class Card extends Controller
{
    //优惠券首页
    public function index()
    {
       $list = Db::name('card')->order('card_id')->paginate(10);
     //   $list = Db::name('card')->group('code')->count('card_id');
      //  var_dump( $list );die;
        $data = $list->toArray()['data'];
        //  var_dump($list);die;
        $count = $list->total();
        $this->assign('count', $count);
    //    $data['nowtime']=time();
        foreach ( $data as $k=>$v){
            $data[$k]['give_number']=$v['card_allnum']-$v['card_num'];
                 //var_dump($data);
        }
   //   var_dump($data);die;
        $this->assign('data',$data);
        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加优惠券
    public function addCard()
    {
        if ($this->request->isPost()) {

            $code='QSSH'.date('YmdHis').mt_rand(1000,9999);
            $data = [
                'card_type' => input('card_type'),
                'card_name' => input('card_name'),
                'card_door' => input('card_door'),
                'card_price' => input('card_price'),
                'card_time' => input('card_time'),
                'starttime' =>strtotime(input('starttime')) ,
                'endtime' =>strtotime(input('endtime')) ,
                'card_limit' => input('card_limit'),
                'limit_num' => input('limit_num'),
                'card_num' =>input('card_num'),
                'card_allnum' =>input('card_num'),
                'all_status' => input('all_status'),
                'code' => $code,
            ];

              $res = Db::name('card')->insert($data);


            if ($res) {
                $this->success('添加成功', 'Card/index', 3);
            } else {
                $this->error('添加失败');
            }
        } else {
          //  $goods_list = Db::name('card')->group('code')->select();
         //  var_dump($goods_list);die;
          //  $this->assign('goods_list', $goods_list );
            return $this->fetch();
        }
    }
  //删除优惠券
    public  function  delCard(){
        $card_id=input('card_id');
        $list= Db::name('card')->where('card_id',$card_id)->find();
        if(empty($list)){
            $this->error('该优惠券不存在');
        }
        $res= Db::name('card')->where('card_id',$card_id)->delete();
        if($res){
            $this->success('删除成功','Card/index',3);
        }else{
            $this->error('删除失败，请联系管理员');
        }
    }

    //得到签名
    public  function  getSign(){
        //  var_dump(111111);die;
        $id= config('AccessKeyId');          // 请填写您的AccessKeyId。
        $key= config('AccessKeySecret');     // 请填写您的AccessKeySecret。
        //var_dump( $id);die;
        // $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $host ='http://'. config('bucket').'.'.config('endpoint');
        //   var_dump( $host);die;
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $callbackUrl = config('callbackUrl');
        //   $callbackUrl = $this->callback();
        $dir = config('dir');          // 用户上传文件时指定的前缀。
        $callback_param = array('callbackUrl'=>$callbackUrl,
            'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType'=>"application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;


        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return json_encode($response);
    }

    public function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    //回调
    public function callback()
    {     // var_dump(555);die;
// 1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";
        /*
         * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改
         * 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行
            RewriteEngine On
            RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
         * */
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL']))
        {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '')
        {
            header("http/1.1 403 Forbidden");
            exit();
        }

// 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

// 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "")
        {
            //header("http/1.1 403 Forbidden");
            exit();
        }

// 4.获取回调body
        $body = file_get_contents('php://input');
        parse_str($body,$body_arr);

// 5.拼接待签名字符串
        $authStr = '';
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if ($pos === false)
        {
            $authStr = urldecode($path)."\n".$body;
        }
        else
        {
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }

// 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1)
        {
            header("Content-Type: application/json");
            $data = array("Status"=>"Ok",'url'=>$body_arr['filename']);
            echo json_encode($data);
        }
        else
        {
            //header("http/1.1 403 Forbidden");
            exit();
        }

    }


}