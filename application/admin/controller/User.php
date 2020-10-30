<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/25
 * Time: 11:27
 */

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\db\Where;

class User extends Common
{
    public function __construct()
    {
        parent::__construct();

    }

    //会员首页
    public function index()
    {

        if ($this->request->isPost()) {
            $mobile = input('post.mobile', 'n');
            $status = input('post.status', '-1');
            $level = input('post.level', '-1');
            $logintime_start = input('post.logintime_start', 'n', 'strip_tags');
            $logintime_end = input('post.logintime_end', 'n', 'strip_tags');
            $lasttime_start = input('post.lasttime_start', 'n', 'strip_tags');
            $lasttime_end = input('post.lasttime_end', 'n', 'strip_tags');

        } else {
            $mobile = input('mobile', 'n');
            $status = input('post.status', '-1');
            $level = input('level', '-1');
            $logintime_start = input('logintime_start', 'n', 'strip_tags');
            $logintime_end = input('logintime_end', 'n', 'strip_tags');
            $lasttime_start = input('lasttime_start', 'n', 'strip_tags');
            $lasttime_end = input('lasttime_end', 'n', 'strip_tags');
        }
        $where = new Where();
        // $where['field']=array('id','remark','status');

        if ($logintime_start != "n" && $logintime_start != "") {

            $logintime_start = strtotime($logintime_start ." 00:00:00");
        }
        if ($logintime_end != "n" &&  $logintime_end != "") {
            $logintime_end = strtotime( $logintime_end . " 23:59:59");
        }
        if (isset($logintime_start) &&  $logintime_start > 0 && isset($logintime_end) &&   $logintime_end > 0) {

            $where['logintime'] = array( 'between', array($logintime_start,$logintime_end ));
            //  var_dump($where['create_time']);
        }

        if ($lasttime_start != "n" &&  $lasttime_start != "") {
            $lasttime_start = strtotime( $lasttime_start ." 00:00:00");

        }
        if ($lasttime_end != "n" && $lasttime_end != "") {
            $lasttime_end = strtotime($lasttime_end . " 23:59:59");
        }
        if (isset( $lasttime_start) &&  $lasttime_start > 0 && isset($lasttime_end) &&   $lasttime_end > 0) {

            $where['lasttime'] = array( 'between', array($lasttime_start,$lasttime_end));
            //  var_dump($where['create_time']);
        }

        //查询会员名称
      if ($mobile != 'n' && $mobile != '') {
            $where[] = ['mobile', 'like', "%" . $mobile . "%"];
        }

        //开启状态
     if ($status > -1) {
            $where['status'] = $status;
        }
        //等级
      if ($level > -1) {
            $where['is_star'] = $level;
        }
     //   var_dump($where);
      //  $order = 'user_id asc';
        $list = Db::name('user')->where($where)->paginate(10, false, ['query' => request()->param()]);

        //echo Db::name('role')->getLastSql();  exit;
      // var_dump($list);die;
        $data = $list->toArray()['data'];
    // var_dump($list);die;
        foreach ($data as $k => $v) {
            $power = Db::name('user_step')->where(array('mobile' => $v['mobile']))->value('power');
            if(empty($power)){
                $data[$k]['power']=0;
            }else{
                $data[$k]['power']=$power;
            }
        }
        $count = $list->total();
 //    var_dump($data);die;
        $this->assign('data', $data);
        $this->assign('list', $list);
        $this->assign('mobile', $mobile);
        $this->assign('status', $status);
        $this->assign('count', $count);
        $this->assign('level', $level);
        $this->assign('logintime_start', $logintime_start);
        $this->assign('logintime_end', $logintime_end);
        $this->assign('lasttime_start', $lasttime_start);
        $this->assign('lasttime_end', $lasttime_end);

        return $this->fetch();

    }

    // 会员详细信息
    public function userInfo()
    {
        $user_id = input('user_id');
        $list = Db::name('user')->where(array('user_id' => $user_id))->find();
        $this->assign('list', $list);
        //var_dump(  $list);die;
        return $this->fetch();

    }

    //更改会员信息
    public function editUser()
    {
        $user_id = input('user_id');
        // var_dump($user_id);
        if ($this->request->isPost()) {
            $data = array(
                'level' => input('level'),
                'status' => input('status'),
                'paypassword' => input('paypassword'),
            );
            $res = Db::name('user')->where(array('user_id' => $user_id))->update($data);
            if ($res) {
                $this->success('修改成功', url('User/index'), 3);

            } else {
                $this->error('修改失败');
            }

        } else {
            $list = Db::name('user')->where(array('user_id' => $user_id))->find();
            if (empty($list)) {
                $this->error('会员不存在');
            }
            $this->assign('list', $list);
            return $this->fetch();
        }

    }

    //改变会员状态
    public function changestatus()
    {
        $user_id = input('user_id');
        $data = array(
            'status' => 0,
        );
        $res = Db::name('user')->where(array('user_id' => $user_id))->update($data);
        if ($res) {
            $this->success('会员已被禁止登录', url('User/index'), 3);
        } else {
            $this->error('网络错误');
        }
    }

    //查看团队
    public function seeteam()
    {
        $user_id = input('user_id');
        $mobile = Db::name('user')->where('user_id', $user_id)->value('mobile');
        if ($this->request->isPost()) {
            $type = input('type', -1, 'intval');
        } else {
            $type = input('type', 1, 'intval');
        }

        //  var_dump($type);die;
        if ($type == 1) {
            $list = Db::name('user')->whereOr('pid', $mobile)->whereOr('ppid', $mobile)->paginate(10, false, ['query' => request()->param($type == 1)]);

            $data = $list->toArray()['data'];
            // var_dump($list);die;
            foreach ($data as $k => $v) {
                if($v['pid'] ==$mobile){
                    $data[$k]['is_pid']=1;
                }else{
                    $data[$k]['is_pid']=2;
                }
            }
            $this->assign('data', $data);
            $this->assign('list', $list);
        } elseif ($type == 2) {
            $list = Db::name('user')->where('pid',$mobile)->paginate(10, false, ['query' => request()->param($type == 2)]);
            //  var_dump($list);die;
            $data = $list->toArray()['data'];
            // var_dump($list);die;
            foreach ($data as $k => $v) {
                if($v['pid'] ==$mobile){
                    $data[$k]['is_pid']=1;
                }else{
                    $data[$k]['is_pid']=2;
                }
            }
            $this->assign('data', $data);
            $this->assign('list', $list);
        } elseif ($type == 3) {
            $list = Db::name('user')->where('ppid', $mobile)->paginate(10, false, ['query' => request()->param($type == 3)]);
            $data = $list->toArray()['data'];
            // var_dump($list);die;
            foreach ($data as $k => $v) {
                if($v['pid'] ==$mobile){
                    $data[$k]['is_pid']=1;
                }else{
                    $data[$k]['is_pid']=2;
                }
            }
            $this->assign('data', $data);
            $this->assign('list', $list);
        }


        $oneinfo = Db::name('user')->field('user_id,pid,ppid')->where('mobile', $mobile)->find();
        //var_dump($pidinfo);die;
        $pidnum = Db::name('user')->where('pid', $mobile)->count('user_id');
        $ppidnum = Db::name('user')->where('ppid', $mobile)->count('user_id');
        $pidnumstar = Db::name('user')->where('pid', $mobile)->where('is_star',1)->count('user_id');
        $ppidnumstar = Db::name('user')->where('ppid', $mobile)->where('is_star',1)->count('user_id');
        // var_dump($ppidnum);die;
        $totalnum = $pidnum + $ppidnum;
        $this->assign('pidnumstar',$pidnumstar);
        $this->assign('ppidnumstar',$ppidnumstar);
        $this->assign('totalnum', $totalnum);
        $this->assign('pidnum', $pidnum);
        $this->assign('ppidnum', $ppidnum);
        $this->assign('mobile', $mobile);
        $this->assign('oneinfo', $oneinfo);
        $this->assign('type', $type);
        return $this->fetch();
        // var_dump( $user_id);die;
    }

    //推荐人列表
    public function active()
    {
        if ($this->request->isPost()) {

        } else {
            $list = Db::name('user')->where('actstatus', 1)->paginate(10);
            //  var_dump($list);die;
            $total = $list->total();
            $this->assign('list', $list);
            $this->assign('count', $total);
            return $this->fetch();
        }
    }

    //添加推荐人身份
    public function addactUser()
    {
        if ($this->request->isPost()) {
            $mobile = input('mobile');
            $list = Db::name('user')->where('mobile', $mobile)->find();
            if (empty($list)) {
                $this->error('该会员不存在');
            }
            $res = Db::name('user')->where('mobile', $mobile)->update(['actstatus' => 1, 'acttime' => time()]);
            if ($res) {
                $this->success('添加成功', 'User/active', 3);
            } else {
                $this->error('添加失败,请联系管理员!');
            }
        } else {

            return $this->fetch();
        }

    }

    //取消推荐人身份
    public function changeact()
    {
        $user_id = input('user_id');
        $res = Db::name('user')->where('user_id', $user_id)->update(['actstatus' => 0]);
        if ($res) {
            $this->success('取消成功', 'User/active', 3);
        } else {
            $this->error('取消失败,请联系管理员!');
        }
    }

    // 处理图片上传
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $validate = config('VALIDATE');
        $path = config('GOODS_PATH');
        $res = $file->validate($validate)->move($path);
        if ($res) {
            $file_path = $res->getPathname();
            return json(['status' => 'success', 'msg' => $file_path]);
        } else {
            return json(['status' => 'fail', 'msg' => $file->getError()]);
        }
    }

    //导出
    public function export(Request $request)
    {
        if ($request->isPost()) {
            $status = input('post.status', -1, 'intval');
            $level = input('post.level', -1, 'intval');
            $mobile = input('post.mobile', 'n', 'strip_tags');
            $logintime_start = input('post.logintime_start', 'n', 'strip_tags');
            $logintime_end = input('post.logintime_end', 'n', 'strip_tags');
            $lasttime_start = input('post.lasttime_start', 'n', 'strip_tags');
            $lasttime_end = input('post.lasttime_end', 'n', 'strip_tags');

        } else {
            $status = input('status', -1, 'intval');
            $level = input('level', -1, 'intval');
            $mobile = input('mobile', 'n', 'strip_tags');
            $logintime_start = input('logintime_start', 'n', 'strip_tags');
            $logintime_end = input('logintime_end', 'n', 'strip_tags');
            $lasttime_start = input('lasttime_start', 'n', 'strip_tags');
            $lasttime_end = input('lasttime_end', 'n', 'strip_tags');
        }

        $where = new Where();
        // $where['field']=array('id','remark','status');

        if ($logintime_start != "n" && $logintime_start != "") {

            $logintime_start = strtotime($logintime_start ." 00:00:00");
        }
        if ($logintime_end != "n" &&  $logintime_end != "") {
            $logintime_end = strtotime( $logintime_end . " 23:59:59");
        }
        if (isset($logintime_start) &&  $logintime_start > 0 && isset($logintime_end) &&   $logintime_end > 0) {

            $where['logintime'] = array( 'between', array($logintime_start,$logintime_end ));
            //  var_dump($where['create_time']);
        }

        if ($lasttime_start != "n" &&  $lasttime_start != "") {
            $lasttime_start = strtotime( $lasttime_start ." 00:00:00");

        }
        if ($lasttime_end != "n" && $lasttime_end != "") {
            $lasttime_end = strtotime($lasttime_end . " 23:59:59");
        }
        if (isset( $lasttime_start) &&  $lasttime_start > 0 && isset($lasttime_end) &&   $lasttime_end > 0) {

            $where['lasttime'] = array( 'between', array($lasttime_start,$lasttime_end));
            //  var_dump($where['create_time']);
        }
        if ($status > -1) {
            $where['status'] = $status;
        }
        if ($level > -1) {
            $where['is_star'] = $level;
        }
        if ($mobile != 'n' && $mobile != '') {
            $where['mobile'] = $mobile;
        }


        $list = Db('user')->field('pid,user_id,mobile,is_star,money,level,logintime,lasttime')->where($where)->order('user_id desc')->select();

        foreach ($list as $k => $v) {
            $pidnum = Db::name('user')->where('pid', $v['mobile'])->count('user_id');
            $ppidnum = Db::name('user')->where('ppid', $v['mobile'])->count('user_id');
            $freezemoney = Db::name('user_freeze_log')->where('status',0)->where('user_id', $v['user_id'])->sum('freezemoney');
            $list[$k]['pidnum'] = $pidnum;
            $list[$k]['ppidnum'] = $ppidnum;
            $list[$k]['totalnum'] = $pidnum+$ppidnum;
            $list[$k]['freezemoney'] = $freezemoney;
        }

        user_exp_excel($width = 1000,8, ['序号', '会员名字', '会员等级', '会员余额', '注册时间', '最后登录时间', '我的上级', '直接推荐人数量', '间接推荐人数量','总人数','冻结金额'], $list, $title = '会员数据');

    }
}