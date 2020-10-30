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
class GoodsType extends Common{
    protected $goods_class;
    public function __construct()
    {
        parent::__construct();
        $this->goods_class = model('GoodsType');
    }
    ///商品分类
    public function index(){

        if ($this->request->isPost()) {
            $class_name = input('post.class_name', 'n');
            $class_status = input('post.class_status', '-1');
        } else {
            $class_name = input('class_name', 'n');
            $class_status = input('class_status', '-1');
        }
        $where = array();
        // $where['field']=array('id','remark','status');
        //查询角色名称
        if ( $class_name != 'n' &&  $class_name != '') {
            $where['class_name'] =  $class_name;
        }

        //开启状态
        if ($class_status > -1) {
            $where['class_status'] = $class_status;
        } else {
            $class_status = -1;
        }
        //var_dump($status);
        $order = 'class_id desc';
        $list = Db::name('goods_class')->where($where)->order($order)->paginate(10);
        //echo Db::name('role')->getLastSql();  exit;
        $count = $list->total();
        $today_start = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $today_end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        //var_dump( $list );die;
        $this->assign('list', $list);
        $this->assign(' class_name',  $class_name);
        $this->assign('class_status', $class_status);
        $this->assign('count', $count);

        //var_dump($class_status);die;
        return $this->fetch();

    }
    //添加分类
    public  function add(){
        if($this->request->isPost()){
            $data=array(
                'class_name'=>input('class_name'),
                'class_desc'=>input('class_desc'),
                'class_status'=>input('class_status',-1,'intval'),
                'add_time'=>time(),
            );

            //var_dump($data);die;
            $res=$this->goods_class->addData($data);
            if($res){
                $this->success('添加分类成功',url('GoodsType/index'),1);
            }else{
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();

        }
    }
    //修改分类
    public  function edit(){
        $class_id = input('class_id', 0, 'intval');

        $goods_one = $this->goods_class->getOne(array('class_id' => $class_id));
        //var_dump(  $goods_one);die;
        if (empty($goods_one)) {
            $this->error('该分类不存在');
        }
        if ($this->request->isPost()) {
            $data=array(
                'class_name'=>input('class_name'),
                'class_desc'=>input('class_desc'),
                'class_status'=>input('class_status',-1,'intval'),

            );
            // dump($data);die;
            $res = $this->goods_class->saveData(array('class_id' => $class_id), $data);
            if ($res) {
                $this->success($data['class_name'] . '修改成功', url('GoodsType/index'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->assign('goods_one',$goods_one);
            return $this->fetch();
        }
    }
    //删除分类
    public  function del(){
        $class_id = input('class_id', 0, 'intval');
        $goods_one = $this->goods_class->getOne(array('class_id' => $class_id));
        //  var_dump($goods_one);die;
        if (empty($goods_one)) {
            $this->error('分类不存在');
        }

        $where = array('class_id' => $class_id);
        $res = $this->goods_class->delData($where);
        if ($res) {
            $this->success('分类删除成功', url('GoodsType/index'), 3);
        } else {
            $this->error('删除失败');
        }
    }
}