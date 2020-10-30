<?php
/*
*xml to array
*/
function xmlToArray($xml)
{       
    $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
    return $array_data;
}

//手机号处理
function suohao($phone){
    $p = substr($phone,0,3)."****".substr($phone,7,4);
    return $p;
}

//身份证号处理
function sfzcard($phone){
    $p = substr($phone,0,3)."***********".substr($phone,14,4);
    return $p;
}
/**
 * 删除指定标签
 *
 * @param array $tags     删除的标签  数组形式
 * @param string $str     html字符串
 * @param bool $content   true保留标签的内容text
 * @return mixed
 */
function stripHtmlTags($tags, $str, $content = true)
{
    $html = [];
    // 是否保留标签内的text字符
    if($content){
        foreach ($tags as $tag) {
            $html[] = '/(<' . $tag . '.*?>(.|\n)*?<\/' . $tag . '>)/is';
        }
    }else{
        foreach ($tags as $tag) {
            $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/is";
        }
    }
    $data = preg_replace($html, '', $str);
    return $data;
}