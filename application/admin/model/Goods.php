<?php
namespace  app\admin\model;
use think\Db;
class Goods extends  Common
{
    protected $table;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table = "goods";
    }

    public function pageData($param = array(), $range = 'total')
    {
        $where = array();
        //父级id
        if (isset($param['pid']) && $param['pid'] > 0) {
            $where[] = array('pid', '=', $param['pid']);
        }
        //判断是否展示
        if (isset($param['is_show']) && $param['is_show'] > -1) {
            $where[] = array('is_show', '=', $param['is_show']);
        }
        //判断level
        if (isset($param['level']) && $param['level'] > 0) {
            $where[] = array('level', '=', $param['level']);
        }
        //求总数
        if ($range == 'total') {
            return Db::name($this->table)->where($where)->count();
        } else {
            //分页
            $limit = isset($param['limit']) ? (int)$param['limit'] : 0;
            if ($limit > 0) {
                $page = isset($param['page']) ? (int)$param['page'] : 1;
                $limit_start = ($page - 1) * $limit;
                $limit = $limit_start . "," . $limit;
            } else {
                $limit = 0;
            }
            //字段
            $field = isset($param['field']) ? $param['field'] : "";
            //排序
            $order = isset($param['order']) ? $param['order'] : 'id asc';
            return Db::name($this->table)->where($where)->field($field)->limit($limit)->order($order)->select();

        }
    }

    /**添加数据
     * @param array $data
     * @return int|string|void
     */
    public function addData($data = array())
    {
        //插入商品基本表
        $goods_id = $this->tableAddData($this->table, $data['basic']);
        //var_dump($goods_id);die;
        if ($goods_id > 0) {

            //商品内容表
            $data['goods_content']['goods_id'] = $goods_id;
            $this->tableAddData('goods_content', $data['goods_content']);
            //添加商品相册
            $data['goods_gallery']['goods_id'] = $goods_id;
            $this->tableAddData('goods_gallery', $data['goods_gallery']);
            //添加参数表
            $goods_parm_data = $data['goods_parm_data'];
            //var_dump( $goods_parm_data);die;
            //$parmname = isset($goods_parm_data['parmname']) ? $goods_parm_data['parmname'] : "";

            if (!empty($goods_parm_data)) {
                $parmname = isset($goods_parm_data['parmname']) ? $goods_parm_data['parmname'] : "";
                $parmvalue = isset($goods_parm_data['parmvalue']) ? $goods_parm_data['parmvalue'] : "";
                if (!empty($parmname)  ) {
                    $goods_parm_data_arr = array();
                    foreach ($parmname as $k => $v) {
                        $goods_parm_data_arr = array(
                            'goods_id' => $goods_id,
                            'parmname' => $parmname[$k],
                            'parmvalue' => $parmvalue[$k],
                        );
                        //var_dump( $goods_parm_data_arr);die;
                        $this->tableAddData('goods_parm', $goods_parm_data_arr);
                    }
                }

            }
            //添加规格表
            $goods_attr_data = $data['goods_attr_data'];

            if(!empty( $goods_attr_data)){
                $attr_stock = isset($goods_attr_data['attr_stock']) ? $goods_attr_data['attr_stock'] : "";
                $attr_specs = isset($goods_attr_data['attr_specs']) ? $goods_attr_data['attr_specs'] : "";
                $ooriginal_price = isset($goods_attr_data['ooriginal_price']) ? $goods_attr_data['ooriginal_price'] : "";
                $ppro_price = isset($goods_attr_data['ppro_price']) ? $goods_attr_data['ppro_price'] : "";
                $attr_points = isset($goods_attr_data['attr_points']) ? $goods_attr_data['attr_points'] : "";
                $attr_code = isset($goods_attr_data['attr_code']) ? $goods_attr_data['attr_code'] : "";
                $attr_bar = isset($goods_attr_data['attr_bar']) ? $goods_attr_data['attr_bar'] : "";
                $attr_weight = isset($goods_attr_data['attr_weight']) ? $goods_attr_data['attr_weight'] : "";
                $attr_ration = isset($goods_attr_data['attr_ration']) ? $goods_attr_data['attr_ration'] : "";
                if(!empty( $attr_stock) ){
                    $goods_attr_data_arr = array();
                    foreach ($attr_stock as $k => $v) {
                        $goods_attr_data_arr = array(
                            'goods_id' => $goods_id,
                            'attr_stock' => $attr_stock[$k],
                            'attr_specs' =>  $attr_specs[$k],
                            'ooriginal_price' =>  $ooriginal_price[$k],
                            'ppro_price' =>  $ppro_price[$k],
                            'attr_points' => $attr_points[$k],
                            'attr_code' =>  $attr_code[$k],
                            'attr_bar' =>  $attr_bar[$k],
                            'attr_weight' =>  $attr_weight[$k],
                            'attr_ration' =>  $attr_ration[$k],
                        );
              //          var_dump( $goods_attr_data_arr);die;
                        $this->tableAddData('goods_attr', $goods_attr_data_arr);
                    }
                    //  var_dump(   $goods_attr_data_arr);die;
                }
            }
        }
    }
//更新商品
    public function saveData ($where = array(),$data = array(),$goods_id = 0)
    {
        //编辑商品基本信息
        $this->tableSaveData($this->table, $where, $data['basic']);
        //更新商品内容
        $goods_content_one = $this->tableGetOne('goods_content', $where);
        if ($goods_content_one) {
            $this->tableSaveData('goods_content', $where, $data['goods_content']);
        } else {
            //商品内容表
            $data['goods_content']['goods_id'] = $goods_id;
            $this->tableAddData('goods_content', $data['goods_content']);
        }

        //添加商品相册
        if (!empty($data['goods_gallery'])) {
            $data['goods_gallery']['goods_id'] = $goods_id;
            //判断商品相册是否存在
            $goods_gallery_one = $this->tableGetOne('goods_gallery', $where);
            if (!empty($goods_gallery_one)) {
                $this->tableSaveData('goods_gallery', $where, $data['goods_gallery']);
            } else {
                $this->tableAddData('goods_gallery', $data['goods_gallery']);
            }

        }else{
            $this->tableDelData('goods_gallery', $where);
        }


        //删除商品原有属性
     $this->tableDelData('goods_parm', $where);
        //添加商品属性
        $goods_parm_data = $data['goods_parm_data'];
        //var_dump( $goods_parm_data);die;
        //$parmname = isset($goods_parm_data['parmname']) ? $goods_parm_data['parmname'] : "";

        if (!empty($goods_parm_data)) {
            $parmname = isset($goods_parm_data['parmname']) ? $goods_parm_data['parmname'] : "";
            $parmvalue = isset($goods_parm_data['parmvalue']) ? $goods_parm_data['parmvalue'] : "";
            if (!empty($parmname)  ) {
                $goods_parm_data_arr = array();
                foreach ($parmname as $k => $v) {
                    $goods_parm_data_arr = array(
                        'goods_id' => $goods_id,
                        'parmname' => $parmname[$k],
                        'parmvalue' => $parmvalue[$k],
                    );
                    //var_dump( $goods_parm_data_arr);die;
                    $this->tableAddData('goods_parm', $goods_parm_data_arr);
                }
            }

        }
        //原有规格不显示

      $this->tableUpdateData('goods_attr', $where);

        //添加规格表
        $goods_attr_data = $data['goods_attr_data'];
        if(!empty( $goods_attr_data)){
            $attr_stock = isset($goods_attr_data['attr_stock']) ? $goods_attr_data['attr_stock'] : "";
            $attr_specs = isset($goods_attr_data['attr_specs']) ? $goods_attr_data['attr_specs'] : "";
            $ooriginal_price = isset($goods_attr_data['ooriginal_price']) ? $goods_attr_data['ooriginal_price'] : "";
            $ppro_price = isset($goods_attr_data['ppro_price']) ? $goods_attr_data['ppro_price'] : "";
            $attr_points = isset($goods_attr_data['attr_points']) ? $goods_attr_data['attr_points'] : "";
            $attr_code = isset($goods_attr_data['attr_code']) ? $goods_attr_data['attr_code'] : "";
            $attr_bar = isset($goods_attr_data['attr_bar']) ? $goods_attr_data['attr_bar'] : "";
            $attr_weight = isset($goods_attr_data['attr_weight']) ? $goods_attr_data['attr_weight'] : "";
            $attr_ration = isset($goods_attr_data['attr_ration']) ? $goods_attr_data['attr_ration'] : "";
            if(!empty( $attr_stock) ){
                $goods_attr_data_arr = array();
                foreach ($attr_stock as $k => $v) {
                    $goods_attr_data_arr = array(
                        'goods_id' => $goods_id,
                        'attr_stock' =>  $attr_stock[$k],
                        'attr_specs' =>  $attr_specs[$k],
                        'attr_points'=>$attr_points[$k],
                        'ooriginal_price' =>  $ooriginal_price[$k],
                        'ppro_price' =>  $ppro_price[$k],
                        'attr_code' =>  $attr_code[$k],
                        'attr_bar' =>  $attr_bar[$k],
                        'attr_weight' =>  $attr_weight[$k],
                        'attr_ration' =>  $attr_ration[$k],
                    );
                    // var_dump($goods_attr_data_arr);die;
               //     var_dump(  $goods_attr_data_arr['attr_points']);die;
                    $this->tableAddData('goods_attr', $goods_attr_data_arr);
                }
            }
        }
    }
}