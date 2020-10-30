<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/14
 * Time: 15:00
 */

namespace app\index\controller;

use think\Db;
use think\Request;
use think\db\Where;
use think\validate;
class user
{
    //我的团队
    public function myteam()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        //  var_dump( $mobile );die;
        $where = [
            'pid' => $mobile,
            'ppid' => $mobile,
        ];
        $list = Db::name('user')->order('logintime desc')->field('is_star,head,level,mobile,logintime')->whereOr('pid', $mobile)->whereOr('ppid', $mobile)->select();
        $allnum = Db::name('user')->field('user_id')->whereOr('pid', $mobile)->whereOr('ppid', $mobile)->count('user_id');
        $pidnum = Db::name('user')->field('user_id')->where('pid', $mobile)->count('user_id');
        $ppidnum = Db::name('user')->field('user_id')->where('ppid', $mobile)->count('user_id');
        //   var_dump($ppidnum );die;
        foreach ($list as $k => $v) {
            $list[$k]['mobile'] = suohao($list[$k]['mobile']);
            $list[0]['allnum'] = $allnum;
            $list[0]['pidnum'] = $pidnum;
            $list[0]['ppidnum'] = $ppidnum;
            //    $list[$k]['mobile']= suohao( $list[$k]['mobile']);
            //    $list[$k]['mobile']= suohao( $list[$k]['mobile']);
        }
        return json($list);
        // var_dump(  $list );die;
    }

    //直接绑定人

    public function pmyteam()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        //  var_dump( $mobile );die;
        $list = Db::name('user')->order('logintime desc')->field('is_star,head,level,mobile,logintime')->where('pid', $mobile)->select();
        foreach ($list as $k => $v) {
            $list[$k]['mobile'] = suohao($list[$k]['mobile']);
        }
        return json($list);
        // var_dump(  $list );die;
    }

    //间接绑定人

    public function ppmyteam()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        //  var_dump( $mobile );die;

        $list = Db::name('user')->order('logintime desc')->field('is_star,head,level,mobile,logintime')->where('ppid', $mobile)->select();
        foreach ($list as $k => $v) {
            $list[$k]['mobile'] = suohao($list[$k]['mobile']);
        }
        return json($list);
        // var_dump(  $list );die;
    }

    //会员升级
    public function userlevel()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $allpower = Db::name('user_step')->where('mobile', $mobile)->value('allpower');
        $list = Db::name('user')->field('head,fashion_level,is_star,nickname')->where('mobile', $mobile)->find();
     /*   if (empty($allpower) || $allpower == 0) {
            return json(['status' => 0, 'msg' => '没有抵抗力,为健康小白', 'is_star' => $list['is_star']]);
        }*/
        //  var_dump();die;
        //  $list = Db::name('user')->field('level,is_star')->where('mobile', $mobile)->find();
        $onelevel = Db::name('fashion_level')->where('fash_level_id', 1)->find();
        $twolevel = Db::name('fashion_level')->where('fash_level_id', 2)->find();
        $threelevel = Db::name('fashion_level')->where('fash_level_id', 3)->find();
        $fourlevel = Db::name('fashion_level')->where('fash_level_id', 4)->find();
        $fivelevel = Db::name('fashion_level')->where('fash_level_id', 5)->find();
        // var_dump(  $twolevel);die;
        $level = $list['fashion_level'];
        /*      if ($level == 0) {
                  if ($list['pricesum'] >= $onelevel['pricesum']) {
                      $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 1, 'viptime' => time()]);
                  }
              } elseif ($level == 1) {
                  $vipnum = Db::name('user')->where('pid', $mobile)->where('level', 1)->count('user_id');
                  // var_dump( $vipnum);die;
                  if ($vipnum >= $twolevel['vipsum']) {
                      $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 2]);
                  }
              } elseif ($level == 2) {
                  $vipnum = Db::name('user')->where('pid', $mobile)->where('level', 1)->count('user_id');
                  $svipnum = Db::name('user')->where('pid', $mobile)->where('level', 2)->count('user_id');
                  if ($vipnum >= $threelevel['vipsum'] || $svipnum >= $threelevel['svipsum']) {
                      $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 3]);
                  }
              }*/
        if ($level == 1) {
            if ($allpower >= $twolevel['num']) {
                $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 2]);
            }
        } elseif ($level == 2) {
            if ($allpower >=$threelevel['num']) {
                $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 3]);
            }
        } elseif ($level == 3) {
            if ($allpower >= $fourlevel['num']) {
                $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 4]);
            }
        } elseif ($level == 4) {
            if ($allpower >= $fivelevel['num']) {
                $res = Db::name('user')->where('mobile', $mobile)->update(['level' => 5]);
            }
        } elseif ($level == 5) {
            return json($list);
        }
        return json($list);
    }

    //用户可提现余额
    public function mymoney()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $user_info = Db::name('user')->field('bankzh,is_real,paypassword,bankaddress,user_id,money,is_bank')->where('mobile', $mobile)->find();
        $freeze_money = Db::name('user_freeze_log')->where('user_id', $user_info['user_id'])->where('status', 0)->sum('freezemoney');
        $user_info['freeze_money'] = number_format($freeze_money,2);
         $user_info['money']=number_format($user_info['money'],2);
        $img = Db::name('advert')->where('advpalce', 5)->value('img');
        $user_info['img'] = $img ;
     //    var_dump($user_info);die;
        return json(['status' => 1, 'info' => $user_info]);
        // $list=Db::name('order')->where('user_id', $user_id)->select();
    }

    //添加银行卡
    public function addbank(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $data = [
                'bankaddress' => input('bankaddress'),
                'bankzh' => input('bankzh'),
            ];
            $mobile = input('mobile');

            $res = Db::name('user')->where('mobile', $mobile)->update($data);
            if ($res) {
                $res1 = Db::name('user')->where('mobile', $mobile)->update(['is_bank' => 1]);
                return json(['status' => 1]);
            } else {
                return json(['status' => 0]);
            }
        }
    }
    //银行卡展示
    public function bank()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $list = Db::name('user')->field('is_bank,user_id,bankaddress,bankzh')->where('mobile', $mobile)->find();
        $name = Db::name('really')->where('user_id', $list['user_id'])->value('name');
        $list['name'] = $name;
        return json($list);
    }

    //用户提现
    public function withdraw()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $list = Db::name('user')->field('paypassword,money,bankaddress,bankzh')->where('mobile', $mobile)->find();
        // var_dump( $list);die;
        /*        if(!empty($list['paypassword'])){
                    return json( ['status'=>1,'info'=>$list]);
                }else{
                    return json( ['status'=>0]);
                }*/
        return json($list);

    }
    //支付密码
    public function paypass(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $mobile = input('mobile');
            $paypassword = md5(input('paypassword'));
            $res = Db::name('user')->where('mobile', $mobile)->update(['paypassword' => $paypassword]);
            if ($res) {
                return json(['status' => 1]);
            } else {
                return json(['status' => 0]);
            }
        }
    }
    //提现
    public function makemoney(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:post'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $mobile = input('mobile');
            $paypassword = md5(input('paypassword'));
            $mvalue = input('mvalue');
            $list = Db::name('user')->where('mobile', $mobile)->find();
            // var_dump($list['paypassword']);die;
            if ($paypassword != $list['paypassword']) {
                return json(['status' => 0, 'info' => '支付密码错误']);
            }
            if ($mvalue <1) {
                return json(['status' => 0, 'info' => '金额小于1元,不可提现!']);
            }
           if($list['is_real'] !=1){
               return json(['status' => 0, 'info' => '未进行实名或者实名未通过,请线实名认证!']);
           }
            $res1 = Db::name('user')->where('mobile', $mobile)->setDec('money', $mvalue);
            $moneydata = [
                'money' => $mvalue,
                'sign' => '-',
                'info' => '用户提现',
                'create_time' => time(),
                'user_id' => $list['user_id'],
            ];
            $res3 = Db::name('user_money')->insert($moneydata);
            $data = [
                'user_id' => $list['user_id'],
                'money' => $mvalue,
                'create_time' => time(),
            ];
            $res2 = Db::name('withdraw')->where('mobile', $mobile)->insert($data);
            if ($res1 && $res2 && $res3) {
                return json(['status' => 1]);
            } else {
                return json(['status' => 0]);
            }
        }
    }

    //更新头像
    public function userhead(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        if ($request->isPost()) {
            $data = [];
            $birthday = input('birthday');
            if (!empty($birthday)) {
                $data['birthday'] = $birthday;
            }
            $nickname = input('nickname');
            if (!empty($nickname)) {
                $data['nickname'] = $nickname;
            }
            $head = input('head');
            if (!empty($head)) {
                $data['head'] = $head;
            }
            $city = input('city');
            if (!empty($city)) {
                $data['city'] = $city;
            }
            //  $list=Db::name('')
            //    var_dump( $data);die;
            $res = Db::name('user')->where('mobile', $mobile)->update($data);
            if ($res) {
                return json(['status' => 1, 'img' => $head]);
            } else {
                return json(['status' => 0]);
            }
        } else {
            $list = Db::name('user')->field('mobile,is_real,is_star,birthday,nickname,head,city')->where('mobile', $mobile)->find();
            $user_id = Db::name('user')->where('mobile', $mobile)->value('user_id');
            $sendnum = Db::name('order')->where('user_id', $user_id)->where('order_status', 1)->count('order_id');
            $takenum = Db::name('order')->where('user_id', $user_id)->where('order_status', 2)->count('order_id');
            $apprisenum = Db::name('order')->where('user_id', $user_id)->where('order_status', 3)->count('order_id');
            return json(['headinfo' => $list, 'sendnum' => $sendnum, 'takenum' => $takenum, 'apprisenum' => $apprisenum]);
        }
    }
    //用户账单
    public function usermoney()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $userinfo = Db::name('user')->field('user_id,head')->where('mobile', $mobile)->find();
        $list = Db::name('user_money')->order('create_time desc')->where('user_id', $userinfo['user_id'])->select();
        foreach ($list as $k => $v) {
            $list[$k]['head']= $userinfo['head'];
        }
        return json($list);
    }

    // 处理图片上传
    public function upload(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $file = $request->file('file');
        //var_dump( $file);die;
            $validate =config('VALIDATE');
         //   var_dump($validate );die;
            $path = config('REALLY_PATH');
            // var_dump($validate['ext'].'==='. $path);die;
          //var_dump($file->validate($validate));die;
            $res = $file->validate($validate)->move($path);
            if ($res) {
                //var_dump( $res);die;
                $file_path = $res->getPathname();
                return json(['status' => 1, 'info' => config('server_name') . '/' . $file_path]);
            } else {

                return json(['status' => 0, 'info' => $file->getError()]);
            }
        }

    }

//用户步数
    public function userstep(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $mobile = input('mobile');
            $step = input('step');
            $data = [
                'mobile' => $mobile,
                'step' => $step,
                'allstep' => input('allstep'),
                'create_time' => time(),
                'finl_time' => time(),
            ];
            $stepinfo = Db::name('user_step')->where('mobile', $mobile)->find();
            if (empty($stepinfo)) {
                $res = Db::name('user_step')->insert($data);
            } else {
                $res = Db::name('user_step')->where('mobile', $mobile)->update(['step' => $step, 'finl_time' => time()]);
                $result = Db::name('user_step')->where('mobile', $mobile)->setInc('allstep', $data['allstep']);
            }
            if ($res) {
                return json(['status' => 1, 'msg' => '步数更新成功']);
            } else {
                   $newdata = [
                'mobile' => $mobile,
                'create_time' => time(),
            ];
                Db::name('user_step_error')->insert($data);
                return json(['status' => 0, 'msg' => '步数更新失败']);
            }
        }

    }

    //步数兑换抵抗力
    public function stepexchange(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
         $step = input('step');
        if ($request->isPost()) {
            $userinfo = Db::name('user_step')->where('mobile', $mobile)->find();
            $ruleinfo = Db::name('rule')->where('rule_id', 1)->find();
            
           if ($userinfo['step'] < 0 && $step ==0 ) {
                return json(['status' => 0, 'msg' => '步数小于0,不能兑换']);
            }
           
           if (!empty($step)) {
              $newpower =  $step / $ruleinfo['step'];       
         } else{
             $newpower =$userinfo['step'] / $ruleinfo['step'];
         }

        // var_dump($newpower);die;
         if($newpower<0.01){
             return json(['status' => 0, 'msg' =>'行走步数太少暂不可以兑换!']);
         }

             $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday= mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        
              $num = Db::name('user_step_log')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday, $endToday])->sum('power');
          // var_dump($ruleinfo['max_power'] );die;
         if( $num >= $ruleinfo['max_power']){
             return json(['status' => 0, 'msg' =>'当日兑换抵抗力已经达到最大值,请明日再来!']);
         }
         $now_power=$userinfo['power']+$newpower;

               $data=[
                 'mobile'=>$mobile,
                 'power'=>$newpower,
                 'info'=>'步数兑换抵抗力',
                 'now_power'=>$now_power,
                 'create_time'=>time(),
            ];
               
             $res1 = Db::name('user_step_log')->insert($data);

            $res = Db::name('user_step')->where('mobile', $mobile)->setInc('power', $newpower);
            $result = Db::name('user_step')->where('mobile', $mobile)->setInc('allpower', $newpower);
            if ($res && $result &&$res1) {
                Db::name('user_step')->where('mobile', $mobile)->update(['step' => 0]);
                return json(['status' => 1, 'msg' => '兑换成功']);
            } else {
                return json(['status' => 0, 'msg' => '兑换失败']);
            }
        } else {
            $power = Db::name('user_step')->field('power')->where('mobile', $mobile)->find();
             $userinfo = Db::name('user')->where('mobile', $mobile)->find();
            $power['is_star']=$userinfo['is_star'];
             $power['head']=$userinfo['head'];;
         //   var_dump( $power);die;
            return json($power);
        }
    }

    //偷取用户的能量   get请求是团队排行
    public function getstep(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $stolenmobile = input('stolenmobile');
        // var_dump( $mobile);die;
        if ($request->isPost()) {
            //获取当天时间戳
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday= mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            $create_time = Db::name('getstep')->where('mobile',$mobile )->where('stolenmobile',$stolenmobile )->order('getstep_id desc')->limit('1')->value('create_time');
            if($create_time> $beginToday  && $create_time <$endToday ){
                return json(['status' => 0, 'msg' => '已经偷取过一次!']);
            }
            $is_star = Db::name('user')->where('mobile', $mobile)->value('is_star');
            if ($is_star != 1) {
                return json(['status' => 0, 'msg' => '不是达人不能摘取!']);
            }
            $step = Db::name('user_step')->where('mobile', $stolenmobile)->value('step');
            if ($step <=1) {
                return json(['status' => 0, 'msg' => '被偷取者步数小于1,不能偷取!']);
            }

            $ruleinfo = Db::name('rule')->where('rule_id', 1)->find();
            $ruleinfo['low'] = $ruleinfo['low'] / 100;
            $ruleinfo['high'] = $ruleinfo['high'] / 100;
            $step = ceil(mt_rand($step * $ruleinfo['low'], $step * $ruleinfo['high']));
        //    $res = Db::name('user_step')->where('mobile', $mobile)->setInc('step', $step);
            if($step <1){
                $step=1;
            }
            $result = Db::name('user_step')->where('mobile', $stolenmobile)->setDec('step', $step);
            $data = [
                'mobile' => $mobile,
                'stolenmobile' => $stolenmobile,
                'sign' => '摘取用户',
                'step' => $step,
                'create_time' => time(),
            ];
            $ress = Db::name('getstep')->insert($data);
            if ( $result && $ress) {
                return json(['status' => 1, 'msg' => '偷取成功','info'=>$step]);
            }
        } else {
            $mobile = input('mobile');
            $pidinfo = Db::name('user')->field('mobile,fashion_level')->where('is_star', 1)->where('pid', $mobile)
                ->select();
            $ppidinfo = Db::name('user')->field('mobile,fashion_level')->where('is_star', 1)->where('ppid', $mobile)
                ->select();
            if (!empty($ppidinfo)) {
                $pidinfo = array_merge($pidinfo, $ppidinfo);
            }
            foreach ($pidinfo as $k => $v) {
                $step = Db::name('user_step')->where('mobile', $v['mobile'])->value('step');
             //   $head = Db::name('user')->where('mobile', $v['mobile'])->value('head');
                $userinfo= Db::name('user')->field('head,is_star')->where('mobile', $v['mobile'])->find();
                $create_time = Db::name('getstep')->where('mobile', $mobile)->where('stolenmobile', $v['mobile'])->order('getstep_id desc')->limit('1')->value('create_time');
                // var_dump( $create_time);die;
                if(empty($step)){
                    $pidinfo[$k]['step'] = 0;
                }else{
                    $pidinfo[$k]['step'] = $step;
                }
                $pidinfo[$k]['head'] = $userinfo['head'];
                $pidinfo[$k]['is_star'] = $userinfo['is_star'];
                $pidinfo[$k]['create_time'] = $create_time;
            }
            // $pidinfo=array_multisort($pidinfo,'step');
            $last_names = array_column($pidinfo, 'step');
            array_multisort($last_names, SORT_DESC, $pidinfo);
            return json($pidinfo);
        }
    }
    //给别人加油
    public function sendoil(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $oilmobile = input('oilmobile');
        if ($request->isPost()) {
            $oil = input('step');
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            //  var_dump($endToday1);die;
            $num = Db::name('sendoil')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday, $endToday])->count('sendoil_id');
            if($num >=3){
                return json(['status' => 0, 'msg' => '今日已为三人助力过,请明日再来!']);
            }
          //  $step = Db::name('user_step')->where('mobile', $mobile)->value('step');
            // if ($step < $oil) {
            //     return json(['status' => 0, 'msg' => '可用步数不足']);
            // }
        //    $res = Db::name('user_step')->where('mobile', $mobile)->setDec('step', $oil);
           // $result = Db::name('user_step')->where('mobile', $oilmobile)->setInc('step', $oil);
            $data = [
                'mobile' => $mobile,
                'oilmobile' => $oilmobile,
                'sign' => '好友助力',
                'step' => $oil,
                'create_time' => time(),
            ];
            $ress = Db::name('sendoil')->insert($data);
            if ($ress) {
                return json(['status' => 1, 'msg' => '加油成功']);
            } else {
                return json(['status' => 0, 'msg' => '加油失败']);
            }
        } else {

            $pidinfo = Db::name('user')->field('mobile')->where('is_star', 1)->where('pid', $mobile)
                ->select();
            $ppidinfo = Db::name('user')->field('mobile')->where('is_star', 1)->where('ppid', $mobile)
                ->select();
            if (!empty($pidinfo)) {
                $pidinfo = array_merge($pidinfo, $ppidinfo);
            }
            foreach ($pidinfo as $k => $v) {
                $step = Db::name('user_step')->where('mobile', $v['mobile'])->value('step');
                $pidinfo[$k]['step'] = $step;
            }
            return json($pidinfo);
        }
    }
     //用户可摘取的能量
       public  function   usablestep(Request $request){
           header("Access-Control-Allow-Origin:*");
           header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
           header('Access-Control-Allow-Headers:x-requested-with, content-type');
           $mobile = input('mobile');
           if ($request->isPost()) {
                 $type=input('type');
                 if($type==1){
                     $getstep_id=input('getstep_id');
                     $res= Db::name('getstep')->where('getstep_id', $getstep_id)->update(['is_get'=>1]);
                 }elseif($type==2){
                     $sendoil_id=input('sendoil_id');
                     $res= Db::name('sendoil')->where('sendoil_id', $sendoil_id)->update(['is_get'=>1]);
                 }elseif($type==3){
                     $lianxu_id=input('lianxu_id');
                     $res= Db::name('user_lianxustep')->where('lianxu_id', $lianxu_id)->update(['is_get'=>1]);
                 }
               if ($res) {
                   return json(['status' => 1]);
               } else {
                   return json(['status' => 0]);
               }
           }else{
                 $list= Db::name('getstep')->where('mobile', $mobile)->where('is_get',0)->select();
                 $oillist= Db::name('sendoil')->where('oilmobile', $mobile)->where('is_get',0)->select();
              //   $signlist= Db::name('user_signin')->field('number,allnumber',true)->where('mobile', $mobile)->where('is_get',0)->select();
                 $lianxulist= Db::name('user_lianxustep')->where('mobile', $mobile)->where('is_get',0)->select();
              //   var_dump($lianxulist);die;
               $arr=[];
                if(!empty($list)){
                  //var_dump(88);die;
                    foreach ($list as $k => $v) {
                        $list[$k]['type']=1;
                    }
                    $arr[0]=$list;
                }else{
                    $arr[0]=[];
                }
               if(!empty($oillist)){
                   foreach ($oillist as $k => $v) {
                       $oillist[$k]['type']=2;
                   }
                   $arr[1]=$oillist;
               }else{
                   $arr[1]=[];
               }
/*               if(!empty($signlist)){
                   foreach ($signlist as $k => $v) {
                       $signlist[$k]['type']=3;
                   }
                   $arr[2]=$signlist;
               }else{
                   $arr[2]=[];
               }*/
               if(!empty($lianxulist)){
                   foreach ( $lianxulist as $k => $v) {
                       $lianxulist[$k]['type']=3;
                   }
                   $arr[3]=$lianxulist;
               }else{
                   $arr[3]=[];
               }
               return json(array_values(array_filter($arr)));
           }
       }
  /*     //清零步数
      public  function   changestep(Request $request){
        $mobile = input('mobile');
        if ($request->isPost()) {
            $res = Db::name('user_step')->where('mobile', $mobile)->update(['step'=>0]);
            if($res) {
                return json(['status' => 1, 'msg' => '清零成功']);
            } else {
                return json(['status' => 0, 'msg' => '清零失败']);
            }
        }
    }*/
    //步数总排行
    public function steprank()
    {
        $mobile = input('mobile');
        $userstep = Db::name('user_step')->field('mobile,allstep')->order('allstep desc')->select();
    //    var_dump($userstep );die;
        foreach ($userstep as $k => $v) {
            $userinfo= Db::name('user')->where('mobile', $v['mobile'])->field('pid,ppid,head,is_star,fashion_level')->find();
         
            $getstep = Db::name('getstep')->field('create_time,step')->where('mobile', $v['mobile'])->order('getstep_id desc')->limit('1')->find();
            if(empty($getstep['step'])){
                 $getstep['step'] =0;
            }
            $userstep[$k]['create_time'] = $getstep['create_time'];
            $userstep[$k]['step'] = $getstep['step'];
            $userstep[$k]['head'] = $userinfo['head'];
            $userstep[$k]['fashion_level'] =$userinfo['fashion_level'];
            $userstep[$k]['is_star'] =$userinfo['is_star'];
            if ($userinfo['pid'] == $mobile  ||  $userinfo['ppid'] == $mobile) {
                $userstep[$k]['is_team'] = 1;
            } else {
                $userstep[$k]['is_team'] = 0;
            }
        }
        return json($userstep);
    }

    //用户签到
    public function usersign(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        if ($request->isPost()) {
            $ruleinfo = Db::name('rule')->where('rule_id', 1)->find();
            $data = [
                'mobile' => $mobile,
                'create_time' => time(),
            ];
            $signinfo = Db::name('user_signin')->where('mobile', $mobile)->find();
            if (empty($signinfo)) {
                $res = Db::name('user_signin')->insert($data);
            } else {
                $number = Db::name('user_signin')->order('sign_id desc')->where('mobile', $mobile)->value('number');
                $allnumber = Db::name('user_signin')->order('sign_id desc')->where('mobile', $mobile)->value('allnumber');
                $create_time = Db::name('user_signin')->order('sign_id desc')->where('mobile', $mobile)->value('create_time');
                $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
                $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
                if ($create_time >= $beginYesterday && $create_time <= $endYesterday) {
                    $sign_id = Db::name('user_signin')->insert($data, 0, 1);
                    $newnumber = $number + 1;
                    $newallnumber =  $allnumber  + 1;
                    $res = Db::name('user_signin')->where('sign_id', $sign_id)->update(['number' => $newnumber]);
                    $res1 = Db::name('user_signin')->where('sign_id', $sign_id)->update(['allnumber'=>$newallnumber]);
                    $number = Db::name('user_signin')->where('sign_id', $sign_id)->value('number');
                    if ($number == 3) {
                        $lianxudata=[
                            'mobile' => $mobile,
                            'step'=>$ruleinfo['three'],
                            'sign'=>'用户连续签到3天',
                            'create_time' => time(),
                            'day'=>3,
                        ];
                        $ress = Db::name('user_lianxustep')->insert($lianxudata);
                        $res = Db::name('user_step')->where('mobile', $mobile)->setInc('step', $ruleinfo['three']);
                        $result = Db::name('user_step')->where('mobile', $mobile)->setInc('allstep', $ruleinfo['three']);
                    } elseif ($number == 7) {
                        $lianxudata=[
                            'mobile' => $mobile,
                            'step'=>$ruleinfo['seven'],
                             'sign'=>'连续签到7天',
                            'create_time' => time(),
                            'day'=>7,
                        ];
                        $ress = Db::name('user_lianxustep')->insert($lianxudata);
                        $res = Db::name('user_step')->where('mobile', $mobile)->setInc('step', $ruleinfo['seven']);
                        $result = Db::name('user_step')->where('mobile', $mobile)->setInc('allstep', $ruleinfo['seven']);
                        Db::name('user_signin')->where('sign_id', $sign_id)->update(['number' => 0]);
                    }
                } else {
                    $res = Db::name('user_signin')->insert($data);
                }
            }
            if ($res) {
                return json(['status' => 1, 'msg' => '签到成功']);
            } else {
                return json(['status' => 0, 'msg' => '签到失败']);
            }
        } else {
        
           $res1=Db::name('user')->where('mobile',$mobile)->update(['lasttime'=>time()]);
            $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
            $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
            $today = strtotime(date("Y-m-d"), time());
            $endtoday = $today + 60 * 60 * 24;
            $list = Db::name('user_signin')->where('mobile', $mobile)->order('sign_id desc')->limit('1')->find();
//            if(empty ($list) ){
//                return json($list);
//            }
            if ($list['create_time'] >= $beginYesterday && $list['create_time'] <= $endYesterday) {
                $result = 1;
            } elseif ($list['create_time'] >= $today && $list['create_time'] <= $endtoday) {
                $result = 2;
            } else {
                $res = Db::name('user_signin')->where('sign_id', $list['sign_id'])->update(['allnumber' => 0]);
            }
            return json($list);
        }

    }
    //摘取记录
    public function todaystep()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $mobile = input('mobile');
        $beginToday1 = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday1 = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
  // var_dump($beginToday1);die;
        $create_time1 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday1, $endToday1])->limit('1')->value('create_time');
        $getstep1 = Db::name('getstep')->where('create_time', 'between', [$beginToday1, $endToday1])->where('mobile', $mobile)->sum('step');
        $newstep1 = Db::name('getstep')->where('create_time', 'between', [$beginToday1, $endToday1])->where('stolenmobile', $mobile)->sum('step');
        $todaystep1= Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday1, $endToday1])->where('stolenmobile', $mobile)->select();
      // var_dump( $todaystep1);die;
            foreach ($todaystep1 as $k => $v){
                $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
                $todaystep1[$k]['nickname'] = $userinfo['nickname'];
                $todaystep1[$k]['head'] = $userinfo['head'];
                $todaystep1[$k]['totalstep'] = $getstep1;
                $todaystep1[$k]['allstep'] = $newstep1 ;
            }
       
        $beginToday2 = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
        $endToday2 = mktime(0, 0, 0, date('m'), date('d') , date('Y')) - 1;
        $create_time2 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday2, $endToday2])->limit('1')->value('create_time');
        $getstep2 = Db::name('getstep')->where('create_time', 'between', [$beginToday2, $endToday2])->where('mobile', $mobile)->sum('step');
        $newstep2 = Db::name('getstep')->where('create_time', 'between', [$beginToday2, $endToday2])->where('stolenmobile', $mobile)->sum('step');
        $todaystep2 = Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday2, $endToday2])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep2 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep2[$k]['nickname'] = $userinfo['nickname'];
            $todaystep2[$k]['head'] = $userinfo['head'];
            $todaystep2[$k]['totalstep'] = $getstep2;
            $todaystep2[$k]['allstep'] = $newstep2 ;
        }
        $beginToday3 = mktime(0, 0, 0, date('m'), date('d')-2, date('Y'));
        $endToday3 = mktime(0, 0, 0, date('m'), date('d')-1 , date('Y')) - 1;
      //  var_dump(  $endToday3 );die;
        $create_time3 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday3, $endToday3])->limit('1')->value('create_time');
        $getstep3 = Db::name('getstep')->where('create_time', 'between', [$beginToday3, $endToday3])->where('mobile', $mobile)->sum('step');
        $newstep3 = Db::name('getstep')->where('create_time', 'between', [$beginToday3, $endToday3])->where('stolenmobile', $mobile)->sum('step');
        $todaystep3 = Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday3, $endToday3])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep3 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep3[$k]['nickname'] = $userinfo['nickname'];
            $todaystep3[$k]['head'] = $userinfo['head'];
            $todaystep3[$k]['totalstep'] = $getstep3;
            $todaystep3[$k]['allstep'] = $newstep3;
        }
        $beginToday4= mktime(0, 0, 0, date('m'), date('d')-3, date('Y'));
        $endToday4 = mktime(0, 0, 0, date('m'), date('d')-2 , date('Y')) - 1;
        //  var_dump(  $endToday3 );die;
        $create_time4 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday4, $endToday4])->limit('1')->value('create_time');
        $getstep4 = Db::name('getstep')->where('create_time', 'between', [$beginToday4, $endToday4])->where('mobile', $mobile)->sum('step');
        $newstep4 = Db::name('getstep')->where('create_time', 'between', [$beginToday4, $endToday4])->where('stolenmobile', $mobile)->sum('step');
        $todaystep4 = Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday4, $endToday4])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep4 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep4[$k]['nickname'] = $userinfo['nickname'];
            $todaystep4[$k]['head'] = $userinfo['head'];
            $todaystep4[$k]['totalstep'] = $getstep4;
            $todaystep4[$k]['allstep'] = $newstep4;
        }
        $beginToday5= mktime(0, 0, 0, date('m'), date('d')-4, date('Y'));
        $endToday5 = mktime(0, 0, 0, date('m'), date('d')-3 , date('Y')) - 1;
        //  var_dump(  $endToday3 );die;
        $create_time5 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday5, $endToday5])->limit('1')->value('create_time');
        $getstep5 = Db::name('getstep')->where('create_time', 'between', [$beginToday5, $endToday5])->where('mobile', $mobile)->sum('step');
        $newstep5 = Db::name('getstep')->where('create_time', 'between', [$beginToday5, $endToday5])->where('stolenmobile', $mobile)->sum('step');
        $todaystep5= Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday5, $endToday5])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep5 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep5[$k]['nickname'] = $userinfo['nickname'];
            $todaystep5[$k]['head'] = $userinfo['head'];
            $todaystep5[$k]['totalstep'] = $getstep5;
            $todaystep5[$k]['allstep'] = $newstep5;
        }
        $beginToday6= mktime(0, 0, 0, date('m'), date('d')-5, date('Y'));
        $endToday6 = mktime(0, 0, 0, date('m'), date('d')-4 , date('Y')) - 1;
        //  var_dump(  $endToday3 );die;
        $create_time6 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday6, $endToday6])->limit('1')->value('create_time');
        $getstep6= Db::name('getstep')->where('create_time', 'between', [$beginToday6, $endToday6])->where('mobile', $mobile)->sum('step');
        $newstep6= Db::name('getstep')->where('create_time', 'between', [$beginToday6, $endToday6])->where('stolenmobile', $mobile)->sum('step');
        $todaystep6= Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday6, $endToday6])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep6 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep6[$k]['nickname'] = $userinfo['nickname'];
            $todaystep6[$k]['head'] = $userinfo['head'];
            $todaystep6[$k]['totalstep'] = $getstep6;
            $todaystep6[$k]['allstep'] = $newstep6;
        }
//        $beginToday6= mktime(0, 0, 0, date('m'), date('d')-5, date('Y'));
//        $endToday6 = mktime(0, 0, 0, date('m'), date('d')-4 , date('Y')) - 1;
//        //  var_dump(  $endToday3 );die;
//        $getstep6= Db::name('getstep')->where('create_time', 'between', [$beginToday6, $endToday6])->where('mobile', $mobile)->sum('step');
//        $newstep6= Db::name('getstep')->where('create_time', 'between', [$beginToday6, $endToday6])->where('stolenmobile', $mobile)->sum('step');
//        $todaystep6= Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday6, $endToday6])->where('stolenmobile', $mobile)->select();
//        foreach ($todaystep6 as $k => $v) {
//            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
//            $todaystep6[$k]['nickname'] = $userinfo['nickname'];
//            $todaystep6[$k]['head'] = $userinfo['head'];
//            $todaystep6[$k]['totalstep'] = $getstep6;
//            $todaystep6[$k]['allstep'] = $newstep6;
//        }
        $beginToday7= mktime(0, 0, 0, date('m'), date('d')-5, date('Y'));
        $endToday7= mktime(0, 0, 0, date('m'), date('d')-4 , date('Y')) - 1;
        //  var_dump(  $endToday3 );die;
        $create_time7 = Db::name('getstep')->where('mobile', $mobile)->where('create_time', 'between', [$beginToday7, $endToday7])->limit('1')->value('create_time');
        $getstep7= Db::name('getstep')->where('create_time', 'between', [$beginToday7, $endToday7])->where('mobile', $mobile)->sum('step');
        $newstep7= Db::name('getstep')->where('create_time', 'between', [$beginToday7, $endToday7])->where('stolenmobile', $mobile)->sum('step');
        $todaystep7= Db::name('getstep')->field('mobile,step,create_time')->where('create_time', 'between', [$beginToday7, $endToday7])->where('stolenmobile', $mobile)->select();
        foreach ($todaystep7 as $k => $v) {
            $userinfo = Db::name('user')->field('nickname,head')->where('mobile', $v['mobile'])->find();
            $todaystep7[$k]['nickname'] = $userinfo['nickname'];
            $todaystep7[$k]['head'] = $userinfo['head'];
            $todaystep7[$k]['totalstep'] = $getstep7;
            $todaystep7[$k]['allstep'] = $newstep7;
        }
        $arr=[];
//        $arr=[
//         0=>$todaystep1,
//         1=>$todaystep2,
//        2=>$todaystep3,
//        3=>$todaystep4,
//          4=>$todaystep5,
//          5=>$todaystep6,
//        6=>$todaystep7,
//    ];
    //    var_dump($todaystep2);die;
        if($getstep1 ==0 &&  $newstep1==0){
            $arr[0]='';
        }else{
            $arr[0]=['status' => 1, 'info' =>[
                'create_time' => $create_time1,
                'totalstep'=> $getstep1,
                'allstep'=> $newstep1 ,
            ],'content'=>$todaystep1];
        }


        if($getstep2 ==0 &&  $newstep2==0){
            $arr[1]=[];
        }else{
            $arr[1]=['status' => 1, 'info' =>[
                'create_time' => $create_time2,
                'totalstep'=> $getstep2,
                'allstep'=> $newstep2 ,
            ],'content'=>$todaystep2];

        }
        if($getstep3 ==0 &&  $newstep3==0){
            $arr[2]=[];
        }else{
            $arr[2]=['status' => 1, 'info' =>[
                'create_time' => $create_time3,
                'totalstep'=> $getstep3,
                'allstep'=> $newstep3 ,
            ],'content'=>$todaystep3];
        }
        if($getstep4 ==0 &&  $newstep4==0){
            $arr[3]=[];
        }else{
            $arr[3]=['status' => 1, 'info' =>[
                'create_time' => $create_time4,
                'totalstep'=> $getstep4,
                'allstep'=> $newstep4 ,
            ],'content'=>$todaystep4];
        }
        if($getstep5 ==0 &&  $newstep5==0){
            $arr[4]=[];
        }else{
            $arr[4]=['status' => 1, 'info' =>[
                'create_time' => $create_time5,
                'totalstep'=> $getstep5,
                'allstep'=> $newstep5 ,
            ],'content'=>$todaystep5];
        }

        if($getstep6 ==0 &&  $newstep6==0){
            $arr[5]=[];
        }else{
            $arr[5]=['status' => 1, 'info' =>[
                'create_time' => $create_time6,
                'totalstep'=> $getstep6,
                'allstep'=> $newstep6,
            ],'content'=>$todaystep6];
        }
        if($getstep7==0 &&  $newstep7==0){
            $arr[6]=[];
        }else{
            $arr[6]=['status' => 1, 'info' =>[
                'create_time' => $create_time7,
                'totalstep'=> $getstep7,
                'allstep'=> $newstep7,
            ],'content'=>$todaystep7];
        }

       // var_dump($arr);die;
   // $arr=array_merge((array)$todaystep1,(array)$todaystep2,(array)$todaystep3,(array)$todaystep4,(array)$todaystep5,(array)$todaystep6,(array)$todaystep7);
        return json(array_values(array_filter($arr)));
     // return json(array_values($arr));
    }
}