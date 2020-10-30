<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//根据订单状态输出状态
function getStatus($order_status){
    if($order_status ==0){
        $str = '未付款';
    }elseif($order_status ==1){
        $str = '已付款';
    }elseif($order_status ==2){
        $str = '已发货';
    }elseif($order_status ==3){
        $str = '已确认收货';
    }elseif($order_status ==4){
        $str = '待评价';
    }elseif($order_status ==5){
        $str = '超时取消';
    }
    return $str;
}
//生成混合字符串
if (!function_exists('getRandKey')) {
    /** 根据随机数生成6位密钥 */
    function getRandKey ($num = false)
    {
        if ($num) {
            $randStr = str_shuffle('1234567890');
            return substr($randStr, 0, 6);
        } else {
            return substr(md5(mt_rand(0, 32) . '0905' . md5(mt_rand(0, 32)) . '0123'), 10, 6);
        }
    }
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
if (!function_exists('get_client_ip')) {
    function get_client_ip ($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}
//展示等级
if(!function_exists('get_level')){
       function get_level($level){
        if($level==1){
            $str = '达人';
        }else {
            $str='注册用户';
        }
        return $str;
       }
}
//展示等级
if(!function_exists('get_pid')){
       function get_pid($pid){
        if($pid==1){
            $str = '直接推荐';
        }else {
            $str='间接推荐';
        }
        return $str;
       }
}
//支付方式
if(!function_exists('get_paytype')){
    function get_paytype($level){
        if($level==1){
            $str = '微信支付';
        }elseif($level==2) {
            $str='支付宝';
        }elseif($level==3) {
            $str='余额支付';
        } elseif($level==4) {
            $str='抵抗力兑换';
        }
        return $str;
    }
}
//时间戳转为时间
if(!function_exists('get_time')){
    function get_time($time){
        if(empty($time)){
           $str='';
        }else{
             $str =date("Y-m-d H:i:s",$time);
        }
           
        return $str;
    }
}
//订单状态

if(!function_exists('get_orderstatus')){
    function get_orderstatus($orderstatus){
        if($orderstatus==0){
            $str = '等待买家付款';
        }elseif($orderstatus==1) {
            $str='买家已付款';
        }elseif($orderstatus==2) {
            $str='卖家已发货';
        }elseif($orderstatus==3) {
            $str='待评价';
        }elseif($orderstatus==4) {
            $str='交易成功';
        }elseif($orderstatus==5) {
            $str='订单已取消';
        }elseif($orderstatus==6) {
            $str='交易成功';
        }elseif($orderstatus==7) {
            $str='已删除';
        }elseif($orderstatus==8) {
            $str='订单处理中';
        }elseif($orderstatus==9) {
            $str='付款超时';
        }
        return $str;
    }
}
//视频第一张
function getCoverImages($fileUrl)
{
    $result = array();

    if (!empty($fileUrl)) {
        $filePath = str_replace("http://img.baidu.cn/", "/data/images/", $fileUrl);
        if (is_file($filePath)) {
            $result = execCommandLine($filePath);
        }
    }
    return json_encode($result);
}

function execCommandLine($file)
{
    $result = array();

    $pathParts = pathinfo($file);
    $filename = $pathParts['dirname'] . "/" . $pathParts['filename'] . "_";
    $times = array(8, 15, 25);
    foreach ($times as $k => $v) {
        $destFilePath = $filename . $v . ".jpg";
        $command = "/usr/bin/ffmpeg -i {$file} -y -f image2 -ss {$v} -vframes 1 -s 640x360 {$destFilePath}";
        exec($command);
        //chmod($filename.$v."jpg",0644);
        $destUrlPath = str_replace("/data/images/", "http://img.baidu.cn/", $destFilePath);
        $selected = $k == 0 ? "1" : "0";//默认将第一张图片作为封面图
        array_push($result, array($destUrlPath, $selected));
    }

    return $result;
}

/**
 * 数组 转 对象
 *
 * @param array $arr 数组
 * @return object
 */
function arrayToObject($arr) {
    if (gettype($arr) != 'array') {
        return;
    }
    foreach ($arr as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object') {
            $arr[$k] = (object)arrayToObject($v);
        }
    }

    return (object)$arr;
}

//生成表格
/**
 * @desc  导出excel 支持导出1000条以上
 * @user  superman
 * @time 2020/2/7 18:52
 * @param int $width
 * @param int $cols
 * @param array $title_arr
 * @param array $data
 * @param string $title
 * @return string
 */
if (!function_exists('exp_excel')) {

    function exp_excel ($width = 1000, $cols = 8, $title_arr = array(), $data = array(), $title = '')
    {
        $exp_data = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html>
                <head>
                <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
                </head>
                <body>
                <table x:str border=1 cellpadding=0 cellspacing=0 style="border-collapse:collapse;table-layout:fixed;width:' . $width . 'pt">
                <tr><td colspan=' . $cols . ' style="height:35pt;font-size:25pt" align="center">' . $title . ' 导出日期：' . date('Y-m-d H:i:s', time()) . '</td></tr>
                <col span=' . $cols . '>
                <tr style="height:18pt">';
        foreach ($title_arr as $val) {
            $exp_data .= '<th>' . $val . '</th>';
        }
        $exp_data .= '</tr>';
        //获取标题头部的个数 然后重置为数组
        $head = range(0, count($title_arr) - 1);
        $key_arr = array_keys($head);
        //判断数据是否存在
        if (empty($data) || !is_array($data)) {
            return '';
        }
        //循环模板数据
        foreach ($data as $k => $v) {
            $exp_data .= '    <tr>';
         //   foreach ($key_arr as $k1 => $v1) {
            $exp_data .= '      <td>' .$v['trade_no'] . '</td>';
            $exp_data .= '      <td>' .$v['goods_name'].'</td>';
            $exp_data .= '      <td>' .$v['ppro_price'] . '</td>';
            $exp_data .= '      <td>' .$v['attr_specs'] . '</td>';
            $exp_data .= '      <td>' .$v['num'] . '</td>';
            $exp_data .= '      <td>' .'姓名:'.$v['name'].'电话:'.$v['addmobile'].'收货地址:'.$v['city'].$v['detailadd'].'</td>';
            $exp_data .= '      <td>' .$v['price'] . '</td>';
            $exp_data .= '      <td>'.get_paytype($v['pay_type']).'</td>';
            $exp_data .= '      <td>' .get_time($v['create_time']) . '</td>';
            $exp_data .= '      <td>' .get_orderstatus($v['order_status']) . '</td>';
            $exp_data .= '      <td>' .$v['exp_name'] . '</td>';
            $exp_data .= '      <td>' .$v['exp_no'] . '</td>';
            $exp_data .= '      <td>' .$v['buymobile'] . '</td>';
         //   }
            $exp_data .= '    </tr>';
        }

        $exp_data .= '</table></body></html>';
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-disposition: attachment;filename=" . $title . "_" . date('Y-m-d') . ".xls");
        header('Cache-Control: max-age=0');
        echo $exp_data;


    }
}

//客服表格
/**
 * @desc  导出excel 支持导出1000条以上
 * @user  superman
 * @time 2020/2/7 18:52
 * @param int $width
 * @param int $cols
 * @param array $title_arr
 * @param array $data
 * @param string $title
 * @return string
 */
if (!function_exists('kefu_exp_excel')) {

    function kefu_exp_excel ($width = 1000, $cols = 8, $title_arr = array(), $data = array(), $title = '')
    {
        $exp_data = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html>
                <head>
                <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
                </head>
                <body>
                <table x:str border=1 cellpadding=0 cellspacing=0 style="border-collapse:collapse;table-layout:fixed;width:' . $width . 'pt">
                <tr><td colspan=' . $cols . ' style="height:35pt;font-size:25pt" align="center">' . $title . ' 导出日期：' . date('Y-m-d H:i:s', time()) . '</td></tr>
                <col span=' . $cols . '>
                <tr style="height:18pt">';
        foreach ($title_arr as $val) {
            $exp_data .= '<th>' . $val . '</th>';
        }
        $exp_data .= '</tr>';
        //获取标题头部的个数 然后重置为数组
        $head = range(0, count($title_arr) - 1);
        $key_arr = array_keys($head);
        //判断数据是否存在
        if (empty($data) || !is_array($data)) {
            return '';
        }
        //循环模板数据
        foreach ($data as $k => $v) {
            $exp_data .= '    <tr>';
         //   foreach ($key_arr as $k1 => $v1) {
            $exp_data .= '      <td>' .$v['trade_no'] . '</td>';
            $exp_data .= '      <td>' .get_time($v['create_time']) . '</td>';
             $exp_data .= '      <td>' .$v['buymobile'] . '</td>';
             $exp_data .= '      <td>' .'&nbsp'. '</td>';
            $exp_data .= '      <td>'.get_paytype($v['pay_type']).'</td>';
             $exp_data .= '      <td>' .get_time($v['pay_time']). '</td>';
              $exp_data .= '      <td>' .$v['adminremark']. '</td>';
               $exp_data .= '      <td>' .$v['remark']. '</td>';
             $exp_data .= '      <td>' .'0'. '</td>';
                    $exp_data .= '      <td>' .$v['price'] . '</td>';

            $exp_data .= '      <td>' .$v['name'] . '</td>'; 
            $exp_data .= '      <td>' .$v['addmobile'] . '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .$v['city'].$v['detailadd']. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 


             $exp_data .= '      <td>' .$v['goods_name'].'</td>';
             $exp_data .= '      <td>' .$v['attr_specs'] . '</td>';
             $exp_data .= '      <td>' .$v['num'] . '</td>';
            $exp_data .= '      <td>' .$v['ppro_price'] . '</td>';
              $exp_data .= '      <td>' .$v['exp_name'] . '</td>';
            $exp_data .= '      <td>' .$v['exp_no'] . '</td>';
             $exp_data .= '      <td>' .get_time($v['send_time']). '</td>';
              $exp_data .= '      <td>' .get_orderstatus($v['order_status']) . '</td>';
                  $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
            $exp_data .= '      <td>' .'&nbsp'. '</td>'; 
          
       
        
           
         //   }
            $exp_data .= '    </tr>';
        }

        $exp_data .= '</table></body></html>';
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-disposition: attachment;filename=" . $title . "_" . date('Y-m-d') . ".xls");
        header('Cache-Control: max-age=0');
        echo $exp_data;


    }
}

//用户表格


/**
 * @desc  导出excel 支持导出1000条以上
 * @user  superman
 * @time 2020/2/7 18:52
 * @param int $width
 * @param int $cols
 * @param array $title_arr
 * @param array $data
 * @param string $title
 * @return string
 */
if (!function_exists('user_exp_excel')) {

    function user_exp_excel ($width = 1000, $cols = 8, $title_arr = array(), $data = array(), $title = '')
    {
        $exp_data = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html>
                <head>
                <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
                </head>
                <body>
                <table x:str border=1 cellpadding=0 cellspacing=0 style="border-collapse:collapse;table-layout:fixed;width:' . $width . 'pt">
                <tr><td colspan=' . $cols . ' style="height:35pt;font-size:25pt" align="center">' . $title . ' 导出日期：' . date('Y-m-d H:i:s', time()) . '</td></tr>
                <col span=' . $cols . '>
                <tr style="height:18pt">';
        foreach ($title_arr as $val) {
            $exp_data .= '<th>' . $val . '</th>';
        }
        $exp_data .= '</tr>';
        //获取标题头部的个数 然后重置为数组
        $head = range(0, count($title_arr) - 1);
        $key_arr = array_keys($head);
        //判断数据是否存在
        if (empty($data) || !is_array($data)) {
            return '';
        }
        //循环模板数据
        foreach ($data as $k => $v) {
            $exp_data .= '    <tr>';
            //   foreach ($key_arr as $k1 => $v1) {
            $exp_data .= '      <td>' .$v['user_id'] . '</td>';
            $exp_data .= '      <td>' .$v['mobile'] . '</td>';
            $exp_data .= '      <td>'. get_level($v['is_star']).'</td>';
              $exp_data .= '      <td>' .$v['money'] . '</td>';
            $exp_data .= '      <td>' .get_time($v['logintime']) . '</td>';
            $exp_data .= '      <td>' .get_time($v['lasttime']) . '</td>';
            $exp_data .= '      <td>' .$v['pid'] . '</td>';
            $exp_data .= '      <td>' .$v['pidnum']. '</td>';
            $exp_data .= '      <td>' .$v['ppidnum']. '</td>';
            $exp_data .= '      <td>' .$v['totalnum']. '</td>';
             $exp_data .= '      <td>' .$v['freezemoney']. '</td>';

            //   }
            $exp_data .= '    </tr>';
        }

        $exp_data .= '</table></body></html>';
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-disposition: attachment;filename=" . $title . "_" . date('Y-m-d') . ".xls");
        header('Cache-Control: max-age=0');
        echo $exp_data;


    }
}
//商品表格


/**
 * @desc  导出excel 支持导出1000条以上
 * @user  superman
 * @time 2020/2/7 18:52
 * @param int $width
 * @param int $cols
 * @param array $title_arr
 * @param array $data
 * @param string $title
 * @return string
 */
if (!function_exists('goods_exp_excel')) {

    function goods_exp_excel ($width = 1000, $cols = 7, $title_arr = array(), $data = array(), $title = '')
    {
        $exp_data = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html>
                <head>
                <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
                </head>
                <body>
                <table x:str border=1 cellpadding=0 cellspacing=0 style="border-collapse:collapse;table-layout:fixed;width:' . $width . 'pt">
                <tr><td colspan=' . $cols . ' style="height:35pt;font-size:25pt" align="center">' . $title . ' 导出日期：' . date('Y-m-d H:i:s', time()) . '</td></tr>
                <col span=' . $cols . '>
                <tr style="height:18pt">';
        foreach ($title_arr as $val) {
            $exp_data .= '<th>' . $val . '</th>';
        }
        $exp_data .= '</tr>';
        //获取标题头部的个数 然后重置为数组
        $head = range(0, count($title_arr) - 1);
        $key_arr = array_keys($head);
        //判断数据是否存在
        if (empty($data) || !is_array($data)) {
            return '';
        }
        $newgoods_name='';
     //   $rowcount= 1;
      //循环模板数据
        foreach ($data as $k => $v) {
            $exp_data .= ' <tr >';
            if($v['goods_name'] == $newgoods_name){
             //   $rowcount+= $rowcount;
                $exp_data .= '<td >'.'</td>';
            }else{
                $exp_data .= '  <td >' .$v['goods_name'] . '</td>';
            }

            $exp_data .= '      <td>' .$v['attr_specs'] . '</td>';
            $exp_data .= '      <td>'.$v['attr_stock'].'</td>';
            $exp_data .= '      <td>' .$v['ooriginal_price'] . '</td>';
            $exp_data .= '      <td>' .$v['ppro_price'] . '</td>';
            $exp_data .= '      <td>' .get_time($v['goods_addtime']) . '</td>';
            $exp_data .= '      <td>' .goods_type($v['goods_type']) . '</td>';
            $exp_data .= '    </tr>';
            $newgoods_name = $v['goods_name'];
        }

        $exp_data .= '</table></body></html>';
        header("Content-type:application/vnd.ms-excel;charset=utf-8");
        header("Content-disposition: attachment;filename=" . $title . "_" . date('Y-m-d') . ".xls");
        header('Cache-Control: max-age=0');
        echo $exp_data;


    }
}
//商品类型
if(!function_exists('goods_type')){
    function goods_type($level){
        if($level==1){
            $str = '实体商品';
        }else {
            $str='抵抗力兑换商品';
        }
        return $str;
    }
}
//生成随机用户名
function get_username()
{
    $chars1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $chars2 = "abcdefghijklmnopqrstuvwxyz0123456789";
    $username = "";
    for ( $i = 0; $i < mt_rand(2,3); $i++ )
    {
        $username .= $chars1[mt_rand(0,25)];
    }
    $username .='_';

    for ( $i = 0; $i < mt_rand(4,6); $i++ )
    {
        $username .= $chars2[mt_rand(0,35)];
    }
    return $username;
}
