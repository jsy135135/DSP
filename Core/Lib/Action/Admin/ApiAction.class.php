<?php

class ApiAction extends OQAction {

    function index($number = null) {
// var_dump($_REQUEST);
        $number = $_REQUEST['_URL_']['3'];
//$number = 18326110041;
        header('Content-Type:text/html;charset=utf-8');
        $url = "http://cx.shouji.360.cn/phonearea.php?number=" . $number . "";
//        k780.com jsonAPI
//        $url = "http://api.k780.com:88/?app=phone.get&phone=" . $number . "&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json";
//        taobao API
//$url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$number;
//        $url = "https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=".$number;
//        var_dump($url);
        $rs = file_get_contents($url);
        echo $rs;
    }

    function lsdspwx($dDate) {
//        var_dump($dDate);
        $url = "http://www.liansuo.com/index.php?act=public&opt=dspwx&par1=".$dDate;
        $str = file_get_contents($url);
        $wx = json_decode($str,true);
//        var_dump($wx); 
        return $wx;
    }
}
