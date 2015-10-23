<?php

/**
 *
 * ━━━━━━神兽出没━━━━━━
 * 　　　┏┓　　　┏┓
 * 　　┏┛┻━━━┛┻┓
 * 　　┃　　　　　　　┃
 * 　　┃　　　━　　　┃
 * 　　┃　┳┛　┗┳　┃
 * 　　┃　　　　　　　┃
 * 　　┃　　　┻　　　┃
 * 　　┃　　　　　　　┃
 * 　　┗━┓　　　┏━┛Code is far away from bug with the animal protecting
 * 　　　　┃　　　┃    神兽保佑,代码无bug
 * 　　　　┃　　　┃
 * 　　　　┃　　　┗━━━┓
 * 　　　　┃　　　　　　　┣┓
 * 　　　　┃　　　　　　　┏┛
 * 　　　　┗┓┓┏━┳┓┏┛
 * 　　　　　┃┫┫　┃┫┫
 * 　　　　　┗┻┛　┗┻┛
 *
 * ━━━━━━感觉萌萌哒━━━━━━
 */
/*
 * 对于内外的一个产品接口
 * Time：2015年3月12日13:45:04
 * By：siyuan
 * 
 */

class ApiAction extends OQAction {
    /*
     * 通过网络获取号码归属地，并进行本地入库操作
     * Time:2015年10月19日14:30:31
     * By:siyuan
     */

    public function internetAttribution($number = null) {
        $mobile = M('mobile');
//        $number = '13354280969';
        $mobileNumber = substr($phone, 0, 7);
//        360号码归属地API
        $url = "http://cx.shouji.360.cn/phonearea.php?number=" . $number . "";
//       k780.com jsonAPI
//        $url = "http://api.k780.com:88/?app=phone.get&phone=" . $number . "&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json";
        $phoneData = curls($url);
        $phoneData = json_decode($phoneData, true);
        $phoneData = $phoneData['data'];
        $inserData = array(
            'mobile' => $mobileNumber,
            'province' => $phoneData['province'],
            'city' => $phoneData['city'],
            'sp' => $phoneData['sp']
        );
        $mobile->add($inserData);
        return $phoneData;
    }

    /*
     * 通过本地数据库，获取号码归属地的接口
     * Time:
     * By:siyuan
     */

    public function getAttribution($phone = null, $type = 1) {
        $mobile = M("mobile");
//        $phone = '13718253509';
        if (isset($phone) && !empty($phone)) {
            $number = substr($phone, 0, 7);
            $rs = $mobile->where("mobile = $number")->field("province,city,sp")->select();
//            if ($rs === NUll) {
//                $rs = $this->internetAttribution($phone);
//            } else {
            $rs = $rs[0];
//            }
            if ($rs['province'] == $rs['city']) {
                $rs['city'] = '';
            }
            if ($type > 0) {
                echo json_decode($rs);
            } else {
                return json_encode($rs);
            }
        } else {
            echo '没有传入号码';
        }
    }

    /*
     *  临时处理分取北京地区号码的方法
     */

    public function fl() {
        $guestbook = M("guestbook");
        $mobile = M("mobile");
        $sql = "SELECT *  FROM `guestbook` WHERE `add_date` >= '2015-10-16' AND `add_date` <= '2015-10-18' AND project_id in (SELECT projectID FROM project WHERE pid = 1)";
        $data = $guestbook->query($sql);
//        dump($data);
//        die();
        $datacount = count($data);
//        die();
        for ($i = 0; $i < $datacount; $i++) {
//            echo 1;
            $number = substr($data[$i]['phone'], 0, 7);
//            echo $number;
            $rs = $mobile->where("mobile = $number")->getField("province");
            if ($rs == '北京') {
                echo $data[$i]['ids'] . '<br />';
            }
        }
    }

    /*
     * 操作现有数据库数据，作废重复数据
     */

    public function quchongfu() {
        set_time_limit(0);
        $g = M("guestbook");
        $date = '2015-09-03';
        $data = $g->where("add_date ='" . $date . "'")->getField("ids,phone,add_date");
        echo count($data) . '<br />';
//        die();
        foreach ($data as $key => $value) {
            $rs = $g->where("phone = '" . $value['phone'] . "' AND add_date = '" . $value['add_date'] . "' AND ids !='" . $value['ids'] . "'")->select();
            if ($rs) {
                $ndata = array('deal_status' => '7', 'u_id' => '0', 'add_date' => '0000-00-00');
                $nrs = $g->where("ids = '" . $value['ids'] . "'")->setField($ndata);
                echo $nrs;
            }
        }
    }

    /*
     * 从数据库到处数据，按照对应规则插入到crm客户表中
     */

    public function importToCrm() {
        set_time_limit(0);
//        $api = 'http://crm.28u1.com/fm/index.php?type=1&m=';
        $sb = M("tp_sb", "", "mysql://root:liansuowang@192.168.200.49:3306/sb");
        $customer = M("crm_customer", "", "mysql://root:liansuowang@192.168.200.49:3306/crm");
        $Data = $sb->select();
        $aes = new AseAction();
        $Data_count = count($Data);
        $data = array();
        for ($i = 254; $i < $Data_count; $i++) {
//            $areaStr = file_get_contents($api . $Data[$i]['phone']);
//            $area = explode(',', $areaStr);
//            $province_name = $area[0];
//            $city_name = $area[1];
            $data[$i]['project_id'] = '1';
            $data[$i]['investment_id'] = '7';
            $data[$i]['username'] = $Data[$i]['name'];
            $data[$i]['phone'] = base64_encode($aes->encode($Data[$i]['phone']));
            $data[$i]['phone_md5'] = $aes->md5_Mcrypt($Data[$i]['phone']);
            $r = $customer->where("phone_md5 = '" . $data[$i]['phone_md5'] . "'")->select();
            if ($r == NULL) {
                $data[$i]['prepeat'] = '1';
            } else {
                $data[$i]['prepeat'] = $r[0]['prepeat'] + 1;
            }
            $data[$i]['province_name'] = '';
            $data[$i]['city_name'] = '';
            $data[$i]['pubdate'] = time();
            $data[$i]['senddate'] = time();
            $data[$i]['message'] = $Data[$i]['content'];
            $data[$i]['media_id'] = '12';
            $data[$i]['ptid'] = $Data[$i]['id'];
            $rs = $customer->add($data[$i]);
            echo $i . '#' . $rs . '<br />';
        }
    }

    /*
     * 给ls返回144外呼后台的录音地址
     */

    public function record_again_ls() {
        $phone = $_REQUEST['phone'];
        $phone = '15932160571';
        $caller_account = '1021';
        $outbound = M("v_call_log_outbound", "", "mysql://root:anlaigz@192.168.200.144:3306/vicidial");
        $recording = M("v_recording_log", "", "mysql://root:anlaigz@192.168.200.144:3306/vicidial");
        $outbound_realtime = M("v_call_log_outbound_realtime", "", "mysql://root:anlaigz@192.168.200.144:3306/vicidial");
        $recording_realtime = M("v_recording_log_realtime", "", "mysql://root:anlaigz@192.168.200.144:3306/vicidial");
        $data = $outbound->where("caller_account = '" . $caller_account . "' AND phone_number = '" . $phone . "'")->select();
        if (!empty($phone)) {
            if ($data == NULL) {
                $data = $outbound_realtime->where("caller_account = '" . $caller_account . "' AND phone_number = '" . $phone . "'")->select();
                $recording_data = $recording_realtime->where("outbound_uniqueid = '" . $data[0]['uniqueid'] . "'")->select();
            } else {
                $recording_data = $recording->where("outbound_uniqueid = '" . $data[0]['uniqueid'] . "'")->select();
            }
            $return_url = $recording_data[0]['location'];
            $return_url = str_replace('192.168.200.144', '120.210.129.20:144', $return_url);
            echo $return_url;
        } else {
            echo '号码传入错误';
        }
    }

    public function test400() {
        $custid = '1115304';
        $Phone400 = new Phone400Action();
        $data = $Phone400->GetCustomer();
        var_dump($data);
    }

    public function Callphone() {
//        $GroupGuid = '078FD35554366E7BE050A8C0CA017514';
        $GroupGuid = 'C19254E5ADD9499794CE7F657011978C';
        $phoneNumber = 18535277952;
        $Phone400 = new Phone400Action();
        $data = $Phone400->CallNumber($GroupGuid, $phoneNumber);
        if ($data) {
            echo '回拨成功';
        } else {
            echo '回拨失败';
        }
//        var_dump($Phone400->Get400DayDetail());
    }

    public function loglist() {
        header("content-type:text/html; charset=UTF-8");
        $date = date('Y-m-d');
        $Phone400 = new Phone400Action();
        $data = $Phone400->GetFreeDayDetail($date);
        var_dump($data);
    }

    function index($number = null) {
//        echo $number;
// var_dump($_REQUEST);
//        $number = $_REQUEST['_URL_']['3'];
//$number = 13354280969;
        header('Content-Type:text/html;charset=utf-8');
        $url = "http://cx.shouji.360.cn/phonearea.php?number=" . $number . "";
//        echo $url;
//        k780.com jsonAPI
//        $url = "http://api.k780.com:88/?app=phone.get&phone=" . $number . "&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json";
//        taobao API
//$url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$number;
//        $url = "https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=".$number;
//        var_dump($url);
//        $rs = file_get_contents($url);
        $rs = $this->curls($url);
        $rs = json_decode($rs, true);
        return $rs['data'];
    }

    /*
     * 此方法作为新对接的呼叫系统的查询信息接口
     * 传入一个电话号码参数
     */

    function phoneinfo() {
        if ($_REQUEST['phone']) {
            $phone = $_REQUEST['phone'];
//        $phone = '18365112833';
            $gb = M("guestbook");
            $data = $gb->where("phone = '" . $phone . "'")->find();
            $phoneinfo['phone'] = $data['phone'];
            $phoneinfo['ip'] = $data['ips'];
            $phoneinfo['url'] = $data['address'];
            $phoneinfo['time'] = $data['times'];
            $phoneinfo['projectID'] = $data['project_id'];
            $phoneinfo['site'] = $data['site'];
            echo json_encode($phoneinfo);
        } else {
            echo '请传入需要查询的电话号码';
        }
//        var_dump($phoneinfo);
    }

    //获取连锁定期返回DSP数据的无效情况
    function lsdspwx($dDate) {
//        var_dump($dDate);
//        $url = "http://www.liansuo.com/index.php?act=public&opt=dspwx&par1=".$dDate;
        //加入par2参数作为识别
        $url = "http://www.liansuo.com/index.php?act=public&opt=dspwx&par2=17668&par1=" . $dDate;
        $str = file_get_contents($url);
        $wx = json_decode($str, true);
//        var_dump($wx); 
        return $wx;
    }

    function tel28($seat, $custid, $phone) {
        $rand = rand();
        $TelUrl = "http://tel.chuangye.com/ah_400_callback.php?seat=" . $seat . "&custid=" . $custid . "&mobile=" . $phone . "&rand=" . $rand . "";
        $stu = file_get_contents($TelUrl);
        return $stu;
    }

    /**
     * 致富网提供的号码查询，是否重复接口
     * Time:2015年3月26日16:41:31
     * By:siyuan
     */
    function repeat_phone($phone = null) {
        $url = "http://saas.zhifuwang.cn/soapclient/hefei.php";
// 参数数组
        $data = array(
            'tel' => "$phone"
        );

        $ch = curl_init();
// print_r($ch);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

}
