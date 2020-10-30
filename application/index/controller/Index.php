<?php
namespace app\index\controller;

use think\Request;
use think\db\Where;
use think\db;

class Index
{
     
    public function search (Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST'); // 这里设置自己的请求方式
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        if ($request->isPost()) {
            $goods_name = input('post.goods_name', 'n');
        } else {
            $goods_name = input('goods_name', 'n');
        }
        $where = array();
        // $where['field']=array('id','remark','status');

        if ( $goods_name != '') {
            $where[] = ['goods_name','like', "%" . $goods_name . "%"];

           // $where[] = ['goods_desc','like', "%" . $goods_name . "%"];
        }
    //    var_dump($where);die;
        $list = Db::name('goods')->where('is_on',1)->where('goods_type',1)->field('goods_id,goods_name,goods_keywords,original_price,pro_price,sale')->where($where)->order('goods_id desc')->select();
        if(!empty($list)){
            foreach ($list as $k => $v) {
                //   var_dump($v['goods_id']);
                $goods_gallery_one = Db::name('goods_gallery')->field('goods_gallery_1')->where(array('goods_id' => $v['goods_id']))->find();
                //  var_dump( $goods_gallery_one);
                $list[$k] = array_merge($list[$k], $goods_gallery_one);
            }
        }
        return json($list);
    }

  /*  public function hello()
    {
        $arr='5';
       for ($i=0; $i <5 ; $i++) { 
            $arr .= $i;
       }
       return $arr;
    }*/

}
