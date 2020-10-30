<?php
//配置文件
return [
    'VALIDATE'=>['ext'=>'jpg,jpeg,gif,png,mp4,wmv,avi,rmvb,rm,FLV'],
    'HEALTHY_PATH'=>'uploads/healthy',
    'HEAD_PATH'=>'uploads/head',
    'REALLY_PATH'=>'uploads/really',
    'ASSESS_PATH'=>'uploads/assess',
    'STATION_PATH'=>'uploads/station',
    'WXAPPID'=>'wx57cbc988073e6bdb',
    'WXSECRET'=>'65dad9738f32b4f8a3a0c03a598f7894',
    'WXMCHID'=>'1577774091',
    'WXKEY'=>'c33367701511b4f6020ec61ded352059',
    //支付宝配置
    // 支付宝
    'ALIPAY_CONFIG' =>[
        'app_id' =>'2021001165673894',    //app_id
        // 支付宝私钥
        'rsaPrivateKey' =>'MIIEowIBAAKCAQEAh+0gV6H+lXtxCcGK3tYtWhCHRrq7aPnUgwcLd+fMjmALmPzPmcKfjcxvBXHkMqGM3CkWw+qVGWzMmDOtnSCNUPQQmTbgsGraWcYz1BP0zUTsIho3QS96mQvwkfye/48brNZpDP8yDuNnyo6WlJDM2UvnZIWh1xheEurwGnddo1zyPBGeTCYiEfb851iWlkxzzG/ROxkHyJWh7x48rwfujZoDvChcdOt2G/YRMMdb/PxorzEEkZ+8epkVXhM6kSbKP1iYrd/0VKf02UAqqRczDWY23xUpyJANKOcAJYt60Gwn10sb+8bor7gSqzMSy22O8OBViK62FnjbBWTdQIH3awIDAQABAoIBABfUqRD0I9BNXhoZxqEe58CcmTr7ThQviOSX/Zi+GQz1JdNir3sDtjmEGAraVunRjQKkaYtbu1xDyPL+THf51wzUXXyfNHF5qmKG71tFTGwYo1WvRvS0wEfI4hqWMcIYcv+wb6aJr1sQ34eRzDCr2l5WgnELRL6TVRr3+bSKLO6RAUsFiTKMOdPU6elR2Fw2/S/AHbQMHCIZ71BmYvV3mDiJqDFiFDKMcRWgvh83a2jUQdV5owrh1gIIv84Wj5+WJu+r3HsetFVHkHvhoGqifoxQ+9IOyXoWCg8gxemJ4C4QMPhq1JtxTSgw6iLkuK1R8vjPRujCL20X01EK/oBbf3ECgYEA0OJUWQyuFAgMhBoK1RK0PTXRnLNgih5q+8+P4Q6zPxjjsi3EdPldx+fg8ZOxwEhhGodZJsKTzqUoENW+3id4VMlShWZ/SYGgyK4CjxS5DgEE6OKB+vExHx6xJvp5CIZNcWqMsCp+mTBEnzVwyuZCEOK88nxsAlIw5OcuzCcnAe8CgYEAppX3dixo8MLetrD3++53EtWY9ZADWTWjsC+8+4M4cOPZSEOzaNPnE7XvhR+RVrhm18Ou2/hhEHXMPJIrU8PJrHPd96yMTd8bL3qOTNXZ87OrrExHtTddaVt4WpwJoTaMtZvwFVHMBJpdKdmwYIlFr/2ZylabjTzsSpXZZCEyrkUCgYEAthKiHSDNV86QQlGE4ac1DimsNR+x5ZKQBEAvFKhAm54xUu1L0f5OvWIkE85+YLF2Wq5hikSOm9Af9VSq02+qFpWJZZgrGUJxiJsMxfT1PPysb+aID9lOzOZu2h/3gfO260ZJrYDM6vBE0FW/pExCh/9rXR4Q85D653uPsgnqmWcCgYAC+VZro6tT8Qas2Ef1FXLGwU1zxNhqdUywzolfLB6L1WWBpsPDMVVEwtC93axoke40F+g9QRfqhU/aHPntCufEzmS+ETSIB12i4Vs8/+xeL2z3LH1zPMPMJ7fkVjjNyf2FGH2Ww9kSk/bp0lsVvh2iYLKoLBem09mcE0TIRtdyyQKBgC0PFNooLa677kkyfQO+ryz2DpyG7vqYPtB8WQbKWqCiOs9mcvu2k1Gv038PvqJNmYnaypQaXQfeMw9nsjRZ4XfeFxLPQsSHwYfpTd0EVgQtc2AyjbBO55zUtImXMOB1/aUpBamwKvt1ZXNslP32EkbTrFGe1iL75OfOCu+1MvEK',
        // 支付宝公钥
        'alipayrsaPublicKey'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh+0gV6H+lXtxCcGK3tYtWhCHRrq7aPnUgwcLd+fMjmALmPzPmcKfjcxvBXHkMqGM3CkWw+qVGWzMmDOtnSCNUPQQmTbgsGraWcYz1BP0zUTsIho3QS96mQvwkfye/48brNZpDP8yDuNnyo6WlJDM2UvnZIWh1xheEurwGnddo1zyPBGeTCYiEfb851iWlkxzzG/ROxkHyJWh7x48rwfujZoDvChcdOt2G/YRMMdb/PxorzEEkZ+8epkVXhM6kSbKP1iYrd/0VKf02UAqqRczDWY23xUpyJANKOcAJYt60Gwn10sb+8bor7gSqzMSy22O8OBViK62FnjbBWTdQIH3awIDAQAB',
        'notify_url' =>'http://qingshi.natapp1.cc/index/order/zfbnotify',//回调地址(支付宝支付成功后回调修改订单状态的地址)
        'payment_type' =>1,//(固定值)
        'charset'    => 'utf-8',//编码
        'sign_type' => 'RSA2',//签名方式
        'version'   =>"1.0",//固定值
        'url'       => 'https://openapi.alipay.com/gateway.do',//固定值
        'method'    => 'alipay.trade.app.pay',                //固定值
    ],

];