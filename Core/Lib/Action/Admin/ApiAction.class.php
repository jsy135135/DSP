<?php
/*
 * 对于内外的一个产品接口
 * Time：2015年3月12日13:45:04
 * By：siyuan
 * 
 */
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
    /*
     * 此方法作为新对接的呼叫系统的查询信息接口
     * 传入一个电话号码参数
     */
    function phoneinfo(){
        if($_REQUEST['phone']){
        $phone = $_REQUEST['phone'];
//        $phone = '18365112833';
        $gb = M("guestbook");
        $data = $gb->where("phone = '".$phone."'")->find();
        $phoneinfo['phone'] = $data['phone'];
        $phoneinfo['ip'] = $data['ips'];
        $phoneinfo['url'] = $data['address'];
        $phoneinfo['time'] = $data['times'];
        $phoneinfo['projectID'] = $data['project_id'];
        $phoneinfo['site'] = $data['site'];
        echo json_encode($phoneinfo);
        }else{
            echo '请传入需要查询的电话号码';
        }
//        var_dump($phoneinfo);
    }
    //获取连锁定期返回DSP数据的无效情况
    function lsdspwx($dDate) {
//        var_dump($dDate);
//        $url = "http://www.liansuo.com/index.php?act=public&opt=dspwx&par1=".$dDate;
        //加入par2参数作为识别
        $url = "http://www.liansuo.com/index.php?act=public&opt=dspwx&par2=17668&par1=".$dDate;
        $str = file_get_contents($url);
        $wx = json_decode($str,true);
//        var_dump($wx); 
        return $wx;
    }
    function tel28($seat,$custid,$phone){
        $rand = rand();
        $TelUrl = "http://tel.chuangye.com/ah_400_callback.php?seat=".$seat."&custid=".$custid."&mobile=".$phone."&rand=".$rand."";
        $stu = file_get_contents($TelUrl);
        return $stu;
    }
}
