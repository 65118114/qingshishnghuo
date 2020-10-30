<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/21
 * Time: 13:58
 */

namespace app\admin\controller;

use think\console\Input;
use think\Controller;
use think\db;

class Rbac extends Common
{
    protected $node;
    protected $role;
    protected  $admin_id;

    public function __construct()
    {
        parent::__construct();
        $this->node = model('Node');
        $this->role = model('Role');
        $this->admin = model('Admin');
        $this->admin_id=input('admin_id');
    }

    public function index()
    {
        if ($this->request->isPost()) {
            $username = input('post.username', 'n');
            $status = input('post.status', '-1');
        } else {
            $username = input('username', 'n');
            $status = input('status', '-1');
        }
        $where = array();
        // $where['field']=array('id','remark','status');
        //查询角色名称
        if ( $username != 'n' &&  $username != '') {
            $where['remark'] =  $username;
        }

        //开启状态
        if ($status > -1) {
            $where['status'] = $status;
        } else {
            $status = -1;
        }
        //var_dump($status);
        $order = 'admin_id desc';
        $list = Db::name('admin')->where($where)->order($order)->paginate(10);
        //echo Db::name('role')->getLastSql();  exit;
        $count = $list->total();
        //  print_r( $list );die;
        //$today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        // $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        /*        if (!empty( $list)) {
                    foreach ( $list as $k => $v) {
                        $list[$k]['role'] = $this->admin->getRole(array('user_id' =>  $list[$k]['admin_id']), array('id', 'name', 'remark'));
                    }
                }*/


        $this->assign('list', $list);
        $this->assign(' username',  $username);
        $this->assign('status', $status);
        $this->assign('count', $count);

        // var_dump($list);die;
        return $this->fetch();


    }

    // 添加管理员
    public function addUser()
    {
        if ($this->request->isPost()) {
            $data = $this->_postUserData();
            $data['login_time']=time();
            $data['login_ip']=get_client_ip();
            $data['add_time']=time();
            $data['password']=input('password');
            $data['salt']=getRandKey();
            $data['password']=md5(md5($data['password']).$data['salt']);
            //var_dump( $data);die;
            //插入数据
            $res=$this->admin->addData($data);
            if($res){
                $role_id=input('role_id');
                $role_data=array();
                if(!empty($role_id)){
                    foreach ($role_id as $v){
                        $role_data[]=array(
                            'role_id'=>$v,
                            'user_id'=>$res,
                        );
                    }
                    $this->role->userRoleAddData($role_data);

                }
                $this->success('添加成功',url('Rbac/index'));
            }else{
                $this->error('添加失败');
            }
        } else {
            $where=array(
                'status'=>1,
                'order'=>'id desc',
            );
            $role_data=$this->role->pageData($where,'range');
            //var_dump($role_data);die;
            $this->assign( 'role_data',$role_data);
            return $this->fetch();
        }
    }
    // 编辑管理员
    public function editUser()
    {
        $admin_one=$this->admin->getOne(array('admin_id'=>$this->admin_id));
        if(empty($admin_one)){
            $this->error('用户不存在');
        }
        if ($this->request->isPost()) {
            $data = $this->_postUserData();
            $data['login_time']=time();
            $data['login_ip']=get_client_ip();
            $data['add_time']=time();
            $data['password']=input('password');
            $data['salt']=getRandKey();
            $data['password']=md5(md5($data['password']).$data['salt']);
            //var_dump( $data);die;
            //插入数据
            $res=$this->admin->saveData(array('admin_id'=>$this->admin_id),$data);
            if($res){
                /*      $role_id=input('role_id');
                      $role_data=array();
                      if(!empty($role_id)){
                          foreach ($role_id as $v){
                              $role_data[]=array(
                                  'role_id'=>$v,
                                  'user_id'=>$res,
                              );
                          }
                          $this->role->userRoleAddData($role_data);

                      }*/
                $this->success('编辑成功',url('Rbac/index'));
            }else{
                $this->error('编辑失败');
            }
        } else {
            $where=array(
                'status'=>1,
                'order'=>'id desc',
            );
            $role_data=$this->role->pageData($where,'range');
            //var_dump($role_data);die;
            $this->assign( 'role_data',$role_data);
            $this->assign( 'admin_one',$admin_one);
            return $this->fetch();
        }
    }
    //删除用户
    public function deleteUser()
    {
        $admin_id=input('admin_id');
        $admin_one=$this->admin->getOne(array('admin_id'=>  $admin_id));
        if(empty($admin_one)){
            $this->error('用户不存在');
        }
        $where = array('admin_id' => $admin_id);
        $result = $this->admin->delData($where);
        if ($result) {
            $this->success('用户删除成功', url('Rbac/index'), 3);
        } else {
            $this->error('删除失败');
        }

    }
    //处理post提交数据
    protected  function _postUserData(){
        $data=array(
            'username'=>input('username'),
            'status'=>input('status',0,'intval'),
            'update_time'=>time(),
        );
        return   $data;
    }

    //角色列表
    public function role()
    {
        if ($this->request->isPost()) {
            $remark = input('post.remark', 'n');
            $status = input('post.status', '-1');
        } else {
            $remark = input('remark', 'n');
            $status = input('status', '-1');
        }
        $where = array();
        // $where['field']=array('id','remark','status');
        //查询角色名称
        if ($remark != 'n' && $remark != '') {
            $where['remark'] = $remark;
        }

        //开启状态
        if ($status > -1) {
            $where['status'] = $status;
        } else {
            $status = -1;
        }
        //var_dump($status);
        $order = 'id asc';
        $list = Db::name('role')->where($where)->order($order)->paginate(10);
        //echo Db::name('role')->getLastSql();  exit;
        $count = $list->total();
        $this->assign('list', $list);
        $this->assign('remark', $remark);
        $this->assign('status', $status);
        $this->assign('count', $count);

        // var_dump($list);die;
        return $this->fetch();

    }

    //添加角色
    public function addRole()
    {
        if ($this->request->isPost()) {
            $data = $this->_postRoleData();
            // dump($data);die;
            $res = $this->role->addData($data);
            if ($res) {
                $this->success($data['remark'] . '添加成功', url('Rbac/role'));
            } else {
                $this->error('添加失败');
            }
        } else {

            return $this->fetch();
        }
    }

    //编辑角色
    public function editRole()
    {
        $id = input('id', 0, 'intval');
        //var_dump($id);die;
        $role_one = $this->role->getOne(array('id' => $id));
        if (empty($role_one)) {
            $this->error('该节点不存在');
        }
        if ($this->request->isPost()) {
            $data = $this->_postRoleData();
            // dump($data);die;
            $res = $this->role->saveData(array('id' => $id), $data);
            if ($res) {
                $this->success($data['remark'] . '添加成功', url('Rbac/role'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $this->assign('role_one', $role_one);
            return $this->fetch();
        }
    }

    //删除角色
    public function deleteRole()
    {
        $id = input('id', 0, 'intval');
        $role_one = $this->role->getOne(array('id' => $id));

        if (empty($role_one)) {
            $this->error('用户不存在');
        }

        $where = array('id' => $id);
        $result = $this->role->delData($where);
        if ($result) {
            $this->success('角色删除成功', url('Rbac/role'), 3);
        } else {
            $this->error('删除失败');
        }

    }

    //处理提交的数据
    private function _postRoleData()
    {
        $data = array(
            'name' => input('name', ''),
            'status' => input('status', 0, 'intval'),
            'remark' => input('remark', '')
        );
        return $data;
    }

    //配置权限
    public function access()
    {
        $rid = input('rid', 0, 'intval');
        $role_one = $this->role->getOne(array('id' => $rid));
        //判断角色是否存在
        if (empty($role_one)) {
            $this->error('该角色不存在');
        }
        //判断是否为post提交
        if ($this->request->isPost()) {
            //删除角色原有权限
            $where = array('role_id' => $rid);
            $this->role->accessDelData($where);
            $data = array();
            $access = input('access');

            foreach ($access as $v) {
                $tmp = explode('_', $v);
                //  echo "<pre>";
                //  var_dump(  $tmp);die;
                $data[] = array(
                    'role_id' => $rid,
                    'node_id'  => $tmp[0],
                    'level' => $tmp[1],
                );
            }
            if ($this->role->accessAddData($data)) {
                $this->success('权限配置成功', url('Rbac/role'), 3);
            } else {
                $this->error('权限配置失败');
            }

        } else {
            //节点列表
            $where['field'] = array('id', 'name', 'title', 'pid');
            $node_data = $this->node->pageData($where, 'range');
            //当前角色的权限
            $field = 'node_id';
            $wherea = array('role_id' => $rid);
            $page_data = $this->role->accessGetField($wherea, $field);
            // var_dump( $page_data);die;
            $page_data = node_merge($node_data, $page_data);

            $this->assign('page_data', $page_data);
            $this->assign('rid', $rid);
            return $this->fetch();

        }

    }

    //节点列表
    public function node()
    {
        $where['field'] = array('id', 'name', 'title', 'pid');
        $page_data = $this->node->pageData($where, 'range');
        $page_data = node_merge($page_data);
        //dump($page_data);die;
        $this->assign('page_data', $page_data);
        return $this->fetch();

    }

    //添加节点
    public function addNode()
    {
        if ($this->request->isPost()) {
            $data = $this->_postNodeData();
            $data['pid'] = input('pid', 0, 'intval');
            $data['level'] = input('level', 1, 'intval');
            // dump($data);die;
            $res = $this->node->addData($data);
            if ($res) {
                $this->success($data['title'] . '添加成功', url('Rbac/node'));
            } else {
                $this->error('添加失败');
            }
        } else {
            $pid = input('pid', 0, 'intval');
            $level = input('level', 1, 'intval');
            switch ($level) {
                case 1:
                    $type = "应用";
                    $parameter = 0;
                    break;
                case 2:
                    $type = "控制器";
                    $parameter = 0;
                    break;
                case 3:
                    $type = "方法";
                    $parameter = 1;
                    break;

            }
            $this->assign('pid', $pid);
            $this->assign('level', $level);
            $this->assign('type', $type);
            $this->assign('parameter', $parameter);

            return $this->fetch();
        }
    }

    //编辑节点
    public function editNode()
    {
        $id = input('id', 0, 'intval');
        //var_dump($id);die;
        $node_one = $this->node->getOne(array('id' => $id));
        if (empty($node_one)) {
            $this->error('该节点不存在');
        }
        if ($this->request->isPost()) {
            $data = $this->_postNodeData();

            // dump($data);die;
            $res = $this->node->saveData(array('id' => $id), $data);
            if ($res) {
                $this->success($data['title'] . '编辑成功', url('Rbac/node'));
            } else {
                $this->error(' 编辑失败');
            }
        } else {

            $level = input('level', 1, 'intval');
            switch ($level) {
                case 1:
                    $type = "应用";
                    $parameter = 0;
                    break;
                case 2:
                    $type = "控制器";
                    $parameter = 0;
                    break;
                case 3:
                    $type = "方法";
                    $parameter = 1;
                    break;

            }

            $this->assign('level', $level);
            $this->assign('type', $type);
            $this->assign('parameter', $parameter);
            $this->assign('node_one', $node_one);
            return $this->fetch();
        }
    }

    private function _postNodeData()
    {
        $data = array(
            'name' => input('name'),
            'title' => input('title'),
            'status' => input('status', 0, 'intval'),
            'sort' => input('sort'),
            'is_show' => input('is_show', 0, 'intval'),
            'parameter' => input('parameter'),
            'parameter_title' => input('parameter_title')
        );

        return $data;
    }

    //删除节点
    public function delNode()
    {
        $id = input('id', 0, 'intval');
        $node_one = $this->node->getOne(array('id' => $id));
        if (empty($node_one)) {
            $this->error('节点不存在');
        }

        //判断当前节点有没有子级分类
        $where = array('pid' => $id);
        // var_dump( $where);die;
        // $page_total=$this->node->pageData($where,'total');
        $page_total = Db::name('node')->where($where)->count();
        if ($page_total > 0) {
            $this->error('该节点有子级节点,不能删除!');
        }
        $where = array('id' => $id);
        $result = $this->node->delData($where);
        if ($result) {
            $this->success('删除成功', url('Rbac/node'), 3);
        } else {
            $this->error('删除失败');
        }

    }


}