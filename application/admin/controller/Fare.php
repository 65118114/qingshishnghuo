<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/25
 * Time: 11:27
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Fare extends Common{
   protected $fare;
   public function __construct()
    {
        parent::__construct();
        $this->fare = model('Fare');
    }
   ///运费模板
    public function index(){

        if ($this->request->isPost()) {
            $username = input('post.username', 'n');
            $status = input('post.status', '-1');
        } else {
            $username = input('username', 'n');
            $status = input('status', '-1');
        }
        $where = array();
        // $where['field']=array('id','remark','status');
        //查询模板名称
        if ( $username != 'n' &&  $username != '') {
            $where['username'] =  $username;
        }

        //开启状态
        if ($status > -1) {
            $where['status'] = $status;
        } else {
            $status = -1;
        }
        //var_dump($status);
        $order = 'fare_id desc';
        $list = Db::name('Fare')->where($where)->order($order)->paginate(10);
        //echo Db::name('role')->getLastSql();  exit;
        $count = $list->total();
        $today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        //var_dump( $list );die;
        $this->assign('list', $list);
        $this->assign(' username',  $username);
        $this->assign('status', $status);
        $this->assign('count', $count);

     //var_dump($class_status);die;
        return $this->fetch();

    }
    //添加运费模板
    public  function addFare(){
        if($this->request->isPost()){
            $data=array(
                'fare_name'=>input('fare_name'),
                'is_free'=>input('is_free'),
                'first_weight'=>input('first_weight'),
                'first_price'=>input('first_price'),
                'continue_weight'=>input('continue_weight'),
                'continue_price'=>input('continue_price'),
                'max_price'=>input('max_price'),
                'status'=>input('status'),
                'addtime'=>time(),
            );

         //   var_dump($data);die;
            $res=$this->fare->addData($data);
             if($res){
                   $this->success('添加模板成功',url('Fare/index'),1);
             }else{
                 $this->error('添加失败');
             }
        }else{
            return $this->fetch();

        }
    }
    //修改模板
    public  function editFare(){
        $id = input('id', 0, 'intval');
   //      var_dump($id);die;
        $fare_one = $this->fare->getOne(array('fare_id' => $id));
      // var_dump(  $fare_one);die;
        if (empty( $fare_one)) {
            $this->error('该模板不存在');
        }
        if ($this->request->isPost()) {
            $data=array(
                'fare_name'=>input('fare_name'),
                'is_free'=>input('is_free'),
                'first_weight'=>input('first_weight'),
                'first_price'=>input('first_price'),
                'continue_weight'=>input('continue_weight'),
                'continue_price'=>input('continue_price'),
                'max_price'=>input('max_price'),
                'status'=>input('status'),
                'addtime'=>time(),

            );
            //dump($data);die;
            $res = $this->fare->saveData(array('fare_id' => $id), $data);
            if ($res) {
                $this->success('修改成功', url('Fare/index'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->assign('fare_one', $fare_one);
            return $this->fetch();
        }
    }
    //删除模板
    public  function delFare(){
        $id = input('id', 0, 'intval');
        $fare_one = $this->fare->getOne(array('fare_id' => $id));
       //  var_dump($goods_one);die;
        if (empty($fare_one)) {
            $this->error('模板不存在');
        }

        $where = array('fare_id' => $id);
        $res = $this->fare->delData($where);
        if ($res) {
            $this->success('模板删除成功', url('Fare/index'), 3);
        } else {
            $this->error('删除失败');
        }
    }
}