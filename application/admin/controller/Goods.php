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

class Goods extends Common{
    protected $goods;
    protected $goodstype;
    protected $fare;
    public function __construct()
    {
        parent::__construct();
        $this->goods = model('Goods');
        $this->goodstype = model('GoodsType');
        $this->fare = model('Fare');
        $this->goods_id = input('goods_id');
    }
    ///商品首页
    public function index(){

        if ($this->request->isPost()) {
            $goods_name = input('post.goods_name', 'n');
            $is_on = input('post.is_on', '-1');
        } else {
            $goods_name = input('goods_name', 'n');
            $is_on = input('is_on', '-1');
        }
        $where = array();
        // $where['field']=array('id','remark','status');
        //查询角色名称
        if ( $goods_name != 'n' &&  $goods_name != '') {
         //   $where['goods_name'] = where('goods_name','like', "%" . $goods_name . "%");
             $where[] = ['goods_name', 'like', "%" . $goods_name . "%"];
        }

        //开启状态
        if ($is_on > -1) {
            $where['class_status'] = $is_on;
        } else {
            $class_status = -1;
        }
        //var_dump($status);
        $order = 'goods_id desc';
        $list = Db::name('goods')->where($where)->order($order)->paginate(10,false,['query'=>request()->param()]);
        //echo Db::name('role')->getLastSql();  exit;
        //var_dump($list);die;
        $count = $list->total();
        $this->assign('list', $list);
        $this->assign('goods_name',$goods_name);
        $this->assign('is_on', $is_on);
        $this->assign('count', $count);

        //var_dump($class_status);die;
        return $this->fetch();

    }
    //添加商品
    public  function addGoods()
    {
        if ($this->request->isPost()) {
            //商品基本表
            $data['basic'] = array(
                'goods_name' => input('goods_name'),
         //       'goods_company' => input('goods_company'),
                'sale' => input('sale'),
                'goods_keywords' => input('goods_keywords'),
                'goods_title' => input('goods_title'),
                'goods_desc' => input('goods_desc'),
                'goods_type' => input('goods_type', '1', 'intval'),
                'goods_cid' => input('goods_cid'),
                'goods_fid' => input('goods_fid'),
                'original_price' => input('original_price'),
                'pro_price' => input('pro_price'),
                'goods_pv' => input('goods_pv'),
                'goods_rule' => input('goods_rule'),
                'is_on' => input('is_on'),
                'goods_addtime' => time(),
            );
            //主图表的数据
            $data['goods_gallery'] = array(
                'goods_gallery_1' =>input('goods_gallery/a')[0],
                'goods_gallery_2' => input('goods_gallery/a')[1],
                'goods_gallery_3' =>input('goods_gallery/a')[2],
                'goods_gallery_4' => input('goods_gallery/a')[3],
                'goods_gallery_5' =>input('goods_gallery/a')[4],

            );
            //  var_dump( $data['goods_gallery']['goods_gallery_1']);die;
            //参数表的数据
            $parmname = input('parmname/a');
            $parmvalue = input('parmvalue/a');

            if ($parmname) {
                foreach ($parmname as $k => $val) {

                    if (!empty($val)) {
                        $data['goods_parm_data']['parmname'][$k] = $val;
                        $data['goods_parm_data']['parmvalue'][$k] = $parmvalue[$k];

                    }
                }
                // var_dump( $data['goods_parm_data']);die;
            } else {
                $data['goods_parm_data'] = [];
            }
            //规格表
            $attr_stock=input('attr_stock/a');
            $attr_specs=input('attr_specs/a');
            $ooriginal_price=input('ooriginal_price/a');
            $ppro_price=input('ppro_price/a');
            $attr_points=input('attr_points/a');
            $attr_code=input('attr_code/a');
            $attr_bar=input('attr_bar/a');
            $attr_weight=input('attr_weight/a');
            $attr_ration=input('attr_ration/a');
            if ($attr_specs) {
                foreach ($attr_specs as $k => $val) {

                    if (!empty($val)) {
                        $data['goods_attr_data']['attr_stock'][$k] = $attr_stock[$k];
                        $data['goods_attr_data']['attr_specs'][$k] = $val;
                        $data['goods_attr_data']['ooriginal_price'][$k] = $ooriginal_price[$k];
                        $data['goods_attr_data']['ppro_price'][$k] = $ppro_price[$k];
                        $data['goods_attr_data']['attr_points'][$k] = $attr_points[$k];

                        $data['goods_attr_data']['attr_code'][$k] = $attr_code[$k];
                        $data['goods_attr_data']['attr_bar'][$k] = $attr_bar[$k];
                        $data['goods_attr_data']['attr_weight'][$k] = $attr_weight[$k];
                        $data['goods_attr_data']['attr_ration'][$k] = $attr_ration[$k];
                    }
                }
              // var_dump( $data['goods_attr_data']['attr_points']);die;
            } else {
                $data['goods_attr_data'] = [];
            }
            //商品内容表
            $data['goods_content'] = array(
                'goods_content' => input('goods_content')
            );
     //   var_dump($data);die;
            //插入数据
            $res = $this->goods->addData($data);
            //   var_dump($res);die;
            //找不到哪里出错,只能先这样做了
            if (!$res) {
                $this->success('添加商品成功', url('Goods/index'), 1);
            } else {
                $this->error('添加失败');
            }

        }
        else{
            $goods_class = Db::name('goods_class')->select();
            $fare = Db::name('fare')->select();
            //var_dump($goods_class);die;
            $this->assign('goods_class', $goods_class);
            $this->assign('fare', $fare);
            return $this->fetch();

        }
    }


    // 处理图片上传
    public  function upload(Request $request)
    {
        $file = $request->file('file');

        $validate=config('VALIDATE');
        $path = config('GOODS_PATH');
        $res=$file->validate($validate)->move($path);
        //var_dump( $res);die;
        if($res){

            $file_path=$res->getPathname();
            //  var_dump( $file_path);die;
            return json(['status'=>'success','msg'=>config('server_name'). '/' . $file_path]);
        }else{
            return json(['status'=>'fail','msg'=>$file->getError()]);
        }
    }
    //修改商品
    public  function editGoods(){
        $goods_id = input('goods_id', 0, 'intval');
        $goods_one = $this->goods->getOne(array('goods_id' => $goods_id));
        $goods_attr =Db::name('goods_attr')->order('attr_id desc')->where(array('goods_id' => $goods_id))->where('is_on',1)->select();
        $goods_content =Db::name('goods_content')->where(array('goods_id' => $goods_id))->find();
        $goods_gallery =Db::name('goods_gallery')->where(array('goods_id' => $goods_id))->find();
        $goods_parm = Db::name('goods_parm')->where(array('goods_id' => $goods_id))->select();
        $goods_class=Db::name('goods_class')->select();
        $fare=Db::name('fare')->select();
        //  var_dump( $goods_gallery);die;
        if (empty($goods_one)) {
            $this->error('该商品不存在');
        }
        if ($this->request->isPost()) {

            //商品基本表
            $data['basic'] = array(
                'goods_name' => input('goods_name'),
              //  'goods_company' => input('goods_company'),
                'sale' => input('sale'),
                'goods_keywords' => input('goods_keywords'),
                'goods_title' => input('goods_title'),
                'goods_desc' => input('goods_desc'),
                'goods_type' => input('goods_type', '1', 'intval'),
                'goods_cid' => input('goods_cid'),
                'goods_fid' => input('goods_fid'),
                'original_price' => input('original_price'),
                'pro_price' => input('pro_price'),
                'goods_pv' => input('goods_pv'),
                'is_on' => input('is_on'),
                'goods_addtime' => time(),
            );
            //主图表的数据
            $goods_gallery_1=config('server_name').input('goods_gallery/a')[0];
            //
            if(  $goods_gallery_1 == config('server_name')){
                $data['goods_gallery'] = array(
                    'goods_gallery_1' =>  $goods_gallery['goods_gallery_1'],
                    'goods_gallery_2' => $goods_gallery['goods_gallery_2'],
                    'goods_gallery_3' => $goods_gallery['goods_gallery_3'],
                    'goods_gallery_4' => $goods_gallery['goods_gallery_4'],
                    'goods_gallery_5' => $goods_gallery['goods_gallery_5'],
                );
            }else{
                $data['goods_gallery'] = array(
                    'goods_gallery_1' =>input('goods_gallery/a')[0],
                    'goods_gallery_2' =>input('goods_gallery/a')[1],
                    'goods_gallery_3' => input('goods_gallery/a')[2],
                    'goods_gallery_4' =>input('goods_gallery/a')[3],
                    'goods_gallery_5' =>input('goods_gallery/a')[4],
                );
            }
            // var_dump(    $data['goods_gallery']);die;


            //参数表的数据
            $parmname = input('parmname/a');
            $parmvalue = input('parmvalue/a');

            if ($parmname) {
                foreach ($parmname as $k => $val) {

                    if (!empty($val)) {
                        $data['goods_parm_data']['parmname'][$k] = $val;
                        $data['goods_parm_data']['parmvalue'][$k] = $parmvalue[$k];

                    }
                }
                // var_dump( $data['goods_parm_data']);die;
            } else {
                $data['goods_parm_data'] = [];
            }
            //规格表
            $attr_stock=input('attr_stock/a');
            $attr_specs=input('attr_specs/a');
            $ooriginal_price=input('ooriginal_price/a');
            $ppro_price=input('ppro_price/a');
            $attr_points=input('attr_points/a');
            $attr_code=input('attr_code/a');
            $attr_bar=input('attr_bar/a');
            $attr_weight=input('attr_weight/a');
            $attr_ration=input('attr_ration/a');
            if ($attr_specs) {
                foreach ($attr_specs as $k => $val) {
                    if (!empty($val)) {
                        $data['goods_attr_data']['attr_stock'][$k] = $attr_stock[$k];
                        $data['goods_attr_data']['attr_specs'][$k] = $val;
                        $data['goods_attr_data']['ooriginal_price'][$k] = $ooriginal_price[$k];
                        $data['goods_attr_data']['ppro_price'][$k] = $ppro_price[$k];
                        $data['goods_attr_data']['attr_points'][$k] = $attr_points[$k];
                        $data['goods_attr_data']['attr_code'][$k] = $attr_code[$k];
                        $data['goods_attr_data']['attr_bar'][$k] = $attr_bar[$k];
                        $data['goods_attr_data']['attr_weight'][$k] = $attr_weight[$k];
                        $data['goods_attr_data']['attr_ration'][$k] = $attr_ration[$k];
                    }
                }
                // var_dump( $data['goods_parm_data']);die;
            } else {
                $data['goods_attr_data'] = [];
            }
            //商品内容表
            $data['goods_content'] = array(
                'goods_content' => input('goods_content')
            );
            //更新数据
       // var_dump(  $data['goods_gallery'] );die;
            $res = $this->goods->saveData(array('goods_id' => $this->goods_id), $data,$this->goods_id);
            //   var_dump($res);die;
            //找不到哪里出错,只能先这样做了
            if (!$res) {
                $this->success('修改商品成功', url('Goods/index'), 1);
            } else {
                $this->error('修改失败');
            }

        } else {
            $this->assign('goods_one',$goods_one);
            // var_dump($goods_parm);die;
            $this->assign('goods_attr',$goods_attr );
            $this->assign('goods_content',$goods_content);
            $this->assign('goods_gallery',$goods_gallery);
            $this->assign('goods_parm',$goods_parm);
            $this->assign('goods_class',$goods_class);
            $this->assign('fare',$fare);
            return $this->fetch();
        }
    }
    //删除商品
    public  function delGoods(){

        $goods_id = input('goods_id', 0, 'intval');
        $goods_one = $this->goods->getOne(array('goods_id' =>$goods_id));
        //  var_dump($goods_one);die;
        if (empty($goods_one)) {
            $this->error('商品不存在');
        }

        $where = array('goods_id' =>$goods_id);
        $res = $this->goods->delData($where);
        if ($res) {
            $this->success('商品删除成功', url('Goods/index'), 3);
        } else {
            $this->error('删除失败');
        }
    }
    //增加评价
    public  function addAssess(){
        if($this->request->isPost()){
            $goods_name=input('goods_name');
            $goods_id=Db::name('goods')->where('goods_name',$goods_name)->value('goods_id');
            //  if(!$goods_id){
            //     $this->error('商品不存在,请重新输入商品名称');
            //  }
            $data=array(
                'username'=>input('username'),
                'content'=>input('content'),
                'status'=>input('status'),
            );
            //   var_dump( $data);die;
            $res=Db::name('assess')->where('goods_id',$goods_id)->insert( $data);
            if($res){
                $this->success('添加成功',url('Goods/index'),3);
            }else{
                $this->error('添加失败');
            }
        }
        return $this->fetch();
    }
    //导出功能
    public  function   export(){
          if ($is_on> -1) {
            $where['is_on'] = $is_on;
        }
        $attr_info = Db::name('goods_attr')->where('is_on',1)->group('goods_id,attr_id')->order('goods_id desc')->select();

        foreach ($attr_info as $k => $v) {
            $list = Db('goods')->field('goods_name,goods_type,goods_addtime')->where('goods_id', $v['goods_id'])->find();
            $attr_info[$k]['goods_name']=$list['goods_name'];
            $attr_info[$k]['goods_type']=$list['goods_type'];
            $attr_info[$k]['goods_addtime']=$list['goods_addtime'];
        }

        goods_exp_excel($width = 1000,7, ['商品名称', '规格名称', '库存', '市场价', '会员价', '添加时间', '商品类型'], $attr_info, $title = '商品数据');
    }
}