<?php

header("Content-type: text/html; charset=utf-8");

/**
 * 自动处理等相关
 *
 */
class TaskAction extends Action {
    /*
     * guestbook 发送给28源数据，根据远程获取域名筛选数据
     * Time:2015年12月16日16:04:28
     * By:siyuan
     */

    public function sync_28dsp_d() {
        set_time_limit(0);
        $domin_array = file_get_contents('http://super.28.com/soap/dsp_400_send/domain.php');
        $domin_array = unserialize($domin_array);
//        var_dump($domin_array);
        $domin_str = implode(',', $domin_array);
        $url = "http://super.28.com/soap/dsp_400_send/phone.php";
        $guestbook = M("guestbook");
        $dsp28 = M("dsp28");
        $TheDate = date('Y-m-d');
        $startDay = date('Y-m-d 09:00:00', strtotime("2 day ago"));
        $endDay = date('Y-m-d 09:00:00', strtotime("1 day ago"));
//        $startDay = '2016-01-23 09:00:00';
//        $endDay = '2016-01-24 09:00:00';
//        $data = $guestbook->where("times >= '" . $startDay . "' AND times <= '" . $endDay . "' AND site = 28 AND project_id > 0")->field('phone,ips,address,times,add_date,project_id')->select();
        $data = $guestbook->where("times >= '" . $startDay . "' AND times <= '" . $endDay . "'")->field('phone,ips,address,times,add_date,project_id')->select();
        $Api = new ApiAction();
        $i = 0;
        foreach ($data as $key => &$value) {
            $domin = parse_url($value['address']);
            $value['domin'] = $domin['host'];
            if (in_array($value['domin'], $domin_array)) {
                $value['site'] = $value['address'];
                $address = json_decode($Api->getAttribution($value['phone'], 0), true);
                $value['address'] = $address['province'] . ' ' . $address['city'];
                $reStatus = curl_post($url, $value);
                $value['status'] = $reStatus;
                $result = $dsp28->add($value);
                $sFileName = "./Log/Dsp28-" . $TheDate . ".txt";
                $fp = fopen($sFileName, "a+");
                fwrite($fp, date("Y-m-d H:i:s") . "#" . "返回值" . $result . "\n");
                fclose($fp);
                $i++;
                unset($value);
            } else {
                continue;
            }
        }
    }

    /*
     * guestbook 发送给28源数据
     * Time:2015年10月14日14:23:34
     * By:siyuan
     */

    public function sync_28dsp() {
        set_time_limit(0);
        $url = "http://super.28.com/soap/dsp_400_send/phone.php";
        $guestbook = M("guestbook");
        $dsp28 = M("dsp28");
        $TheDate = date('Y-m-d');
        $startDay = date('Y-m-d 09:00:00', strtotime("2 day ago"));
        $endDay = date('Y-m-d 09:00:00', strtotime("1 day ago"));
//        $startDay = '2015-11-19 09:00:00';
//        $endDay = '2015-11-20 09:00:00';
        $data = $guestbook->where("times >= '" . $startDay . "' AND times <= '" . $endDay . "' AND site = 28 AND project_id > 0")->field('phone,ips,address,times,add_date,project_id')->select();
//        dump($data);
//        die();
        $Api = new ApiAction();
        $i = 0;
        foreach ($data as $key => &$value) {
            $value['site'] = $value['address'];
            $address = json_decode($Api->getAttribution($value['phone'], 0), true);
            $value['address'] = $address['province'] . ' ' . $address['city'];
            $reStatus = curl_post($url, $value);
            $value['status'] = $reStatus;
            $result = $dsp28->add($value);
            $sFileName = "./Log/Dsp28-" . $TheDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "返回值" . $result . "\n");
            fclose($fp);
            $i++;
            unset($value);
        }
    }

    /*
     * guestbook同步到aliyun by用户网址
     * Time:2015年7月28日11:12:18
     * by：siyuan
     */

    public function sync_aliyun() {
        $master = M("guestbook");
        $salver = M("gb", "tj_", "mysql://root:!@#kingbone$%^@182.92.150.169:3306/tongji");
        $wz = M("wz", "tj_", "mysql://root:!@#kingbone$%^@182.92.150.169:3306/tongji");
        $wz_data = $wz->select();

        foreach ($wz_data as $key => $value) {
//            echo $value['wz'].'<br />';
            $iMaxID = $salver->query("SELECT MAX(ids) as maxid FROM tj_gb where 1");
            $iMaxID = $iMaxID[0]['maxid'];
            $data = $master->query("SELECT * FROM guestbook where address like '%" . $value['wz'] . "%' AND ids > '" . $iMaxID . "'");
            $rs = $salver->addAll($data);
            if ($rs) {
                echo "成功";
            } else {
                echo "失败";
            }
        }
    }

    /*
     * guestbook同步到aliyun by用户网址
     * Time:2015年8月10日13:35:07
     * by：siyuan
     */

    public function sync_aliyun2() {
        $master = M("guestbook");
        $salver = M("guestbook", "tj_", "mysql://sy:syys@182.92.150.169:3306/tjjt");
        $wz = M("wz", "tj_", "mysql://sy:syys@182.92.150.169:3306/tjjt");
        $wz_data = $wz->query("SELECT DISTINCT wz from tj_wz");
        $iMaxID = $salver->query("SELECT MAX(ids) as maxid FROM tj_guestbook where 1");
        $iMaxID = $iMaxID[0]['maxid'];
        foreach ($wz_data as $key => $value) {
            $data = $master->query("SELECT ids,phone,ips,address,times,add_date,project_id,site FROM guestbook where address like '%" . $value['wz'] . "%' AND ids > '" . $iMaxID . "'");
            $data_count = count($data);
//            echo $value['wz'].'#####'.$data_count.'<br />';
            for ($i = 0; $i < $data_count; $i++) {
                $rs = $salver->add($data[$i]);
            }
            if ($rs) {
                echo "成功";
            } else {
                echo "失败";
            }
        }
    }

    /*
     * 获取远程数据库的数据，并导入到本地数据库
     * Time:2015-6-12 14:40:00
     * By：siyuan
     */

    public function sync_s2() {
        $master = M("guestbook");
//        $salver = M("gbook", "", "mysql://root:!@#kingbone$%^@182.92.150.169:3306/gbook");
        $salver = M("gbook", "", "mysql://aNXYWcKC:uA9SWZQtC3jORpNT@218.246.18.4:3306/tj.91jmw.com");
        $iMaxID = $master->query("SELECT MAX(s2_id) as maxid FROM guestbook where 1");
        $iMaxID = $iMaxID[0]['maxid'];
//        echo $iMaxID;
        $data = $salver->query("SELECT phone,id,ip,r,keywords,referrer,time FROM gbook WHERE id > " . $iMaxID . " group by phone order by id asc LIMIT 20000");
//        var_dump($data);
//        die();
//        echo $salver->getLastSql();
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            //如果来源链接为空，则作废，跳出循环，不做入库操作
            if (empty($data[$i]['referrer'])) {
                continue;
            }
            $data[$i]['phone'] = trim($data[$i]['phone']);
            if (strlen($data[$i]['phone']) == 13) {
                $data[$i]['phone'] = substr($data[$i]['phone'], 2, 11);
            }
//            echo $data[$i]['phone'].'<br />';
            $starttime = date("Y-m-d H:m:s", strtotime("" . $data[$i]['time'] . "-24 hour"));
//            $endtime = date("Y-m-d H:m:s", strtotime("".$data[$i]['time']."+24 hour"));
            $rs = $master->where("phone = '" . $data[$i]['phone'] . "' AND times >='" . $starttime . "'")->select();
//            $rs = $master->where("phone = '" . $data[$i]['phone'] . "'")->select();
//            echo $master->getLastSql();
//            var_dump($rs).'<br />';
            //如果存在的话，就跳出此次循环，不进行本地数据库的插入操作
            if ($rs) {
                echo '库里号码重复了';
                continue;
            }
            $aData = array();
            $aData['phone'] = $data[$i]['phone'];
            $aData['s2_id'] = $data[$i]['id'];
            $aData['source'] = '1';
            $aData['ips'] = $data[$i]['ip'];
            $aData['address'] = $data[$i]['referrer'];
            $aData['province'] = '';
            $aData['keywords'] = $data[$i]['keywords'];
            $aData['times'] = $data[$i]['time'];
            $date = date("Y-m-d", strtotime($data[$i]['time']));
            $aData['add_date'] = $date;
            $aData['update_date'] = $date;
            $re = $master->add($aData);
            unset($aData);
            echo $re . '<br />';
        }
//        var_dump($data);
    }

    /*
     * crm [客户关系管理系统]
     * Copyright (c) 2013
     * Author：siyuan
     * 模块名称：定时操作数据库，导入数据
     */

    //定时从源数据总库，查出规定的项目的数据
    public function dspforc() {
        $guestbook = M("guestbook");
        $gb_c = M("gb_c");
        $user = M("user");
        $date = $date = date("Y-m-d");
        $imax = $gb_c->query("select max(ids) as max from gb_c");
        $imax = $imax[0]['max'];
        $project_id = $user->where("role = '13'")->getField("remark");
        $data = $guestbook->where("project_id = '" . $project_id . "' AND site = '91' AND add_date = '" . $date . "' AND ids >'" . $imax . "'")->select();
        echo $gb_c->addAll($data);
    }

    #DSP数据特定项目的数据输出（此为介护宝项目）

    public function sysforycrm() {
        $guestbook = M("guestbook");
        $date = date("Y-m-d", strtotime("1 days ago"));
//        $date = '2014-12-28';
        $data = $guestbook->query("select ids,phone,add_date from guestbook where site = '91' AND project_id = 387 AND add_date = '" . $date . "'");
        $data = serialize($data);
        echo $data;
    }

    #进行前一天一堆多数据的输出

    public function sysfordcrm() {
        $guestbook = M("guestbook");
        $date = date("Y-m-d", strtotime("1 days ago"));
//        $dsql = "SELECT ids,phone,add_date FROM `guestbook` WHERE `add_date` = '".$date."' AND `project_id` = ''";
        $dsql = "SELECT ids,phone,add_date FROM guestbook WHERE add_date = '" . $date . "'AND project_id = '' AND ids >= ((SELECT MAX(ids) FROM guestbook)-(SELECT MIN(ids) FROM guestbook)) * RAND() + (SELECT MIN(ids) FROM guestbook)  LIMIT 400";
        $data = $guestbook->query($dsql);
        $data = serialize($data);
        echo $data;
    }

    public function transferlist() {
        $transfer = M("transfer");
        $date = date("Y-m-d", strtotime("1 days ago"));
//        echo $date;
        $data = $transfer->where("indate = '" . $date . "'")->select();
//        echo $data->getLastsql;
        $datacount = count($data);
        echo '<table>';
        echo '<tr><td>记录自增ID</td><td>对应联展400电话表字段ID</td><td>手机电话</td><td>拨打日期</td><td>拨打时间</td><td>呼叫的状态</td><td>通话时长</td><td>省份</td><td>城市</td><td>标识</td></tr>';
        for ($i = 0; $i < $datacount; $i++) {
            echo '<tr>';
            echo '<td>' . $data[$i]["t_id"] . '</td><td>' . $data[$i]["id"] . '</td><td>' . $data[$i]["tel"] . '</td><td>' . $data[$i]["indate"] . '</td><td>' . $data[$i]["dtime"] . '</td><td>' . $data[$i]["state"] . '</td><td>' . $data[$i]["calltime"] . '</td><td>' . $data[$i]["province"] . '</td><td>' . $data[$i]["city"] . '</td><td>' . $data[$i]["identify"] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function sys_transfer() {
        $url = 'http://super.28.com/soap/dsp_400_send/send.php';
//        $url = 'http://super.28.com/soap/dsp_400_send/send.php?dt=2014-10-2';
        $data = $this->getWebData($url);
//        var_dump($data);
        $transfer = M("transfer");
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            $rs = $transfer->add($data[$i]);
            var_dump($rs);
        }
    }

    #curl获取url信息的方法，返回数组形式

    public function getWebData($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $ar = unserialize($output);
        return $ar;
    }

    public function sys_toCCenter() {
        $project = M("project");
        $sql = "SELECT c.id,c.catname,p.name,p.backCall FROM `project` as p  left join category as c on p.cid = c.id WHERE `site` LIKE '28' AND p.status =1 AND p.backCall <>0 order by c.id asc,p.name asc";
        $data = $project->query($sql);
        $industrydata = $data;
//        var_dump($data);
//        die();
        foreach ($industrydata as &$value) {
            $value["industryid"] = $value["id"];
            unset($value["id"]);
            $value["industry"] = $value["catname"];
            unset($value["catname"]);
            $value["presence"] = 1;
            unset($value["backCall"]);
            unset($value["name"]);
        }
//        $industrydata = array_unique($industrydata);
//        var_dump($data);
        var_dump($industrydata);
    }

    public function sys_toCallCenter() {
        $this->sys_industry();
        $this->sys_company();
        echo '呼叫中心数据同步完成';
    }

    public function sys_industry() {
        $category = M("category");
        $sql = "SELECT id,catname FROM `category`";
        $data = $category->query($sql);
        var_dump($data);
        die();
        foreach ($data as &$value) {
            $value["industryid"] = $value["id"];
            unset($value["id"]);
            $value["industry"] = $value["catname"];
            unset($value["catname"]);
            $value["presence"] = 1;
        }
//        var_dump($data);
        $agent = M('industry', 'vtiger_', 'mysql://root:anlaigz@192.168.200.144:3306/crm_default');
//                $dd = $agent->where("1")->select();
//                var_dump($dd);
        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
//                    var_dump($data[$i]);
            $id = $agent->where("industry = '" . $data[$i]["industry"] . "'")->getField("id");
            if ($id > 0) {
//                echo '修改';
                $agent->where("id=" . $id)->save($data[$i]);
            } else {
//                echo '增加';
                $agent->add($data[$i]);
            }
            unset($data[$i]);
        }
    }

    public function sys_company() {
        $project = M("project");
        $sql = "SELECT c.id,p.name,p.backCall FROM `project` as p  left join category as c on p.cid = c.id WHERE `site` LIKE '28' AND p.status =1 AND p.backCall <>0";
        $data = $project->query($sql);
//                var_dump($data);
        foreach ($data as &$value) {
//                    var_dump($value);
            $value["company"] = $value["name"];
            unset($value["name"]);
            $value["presense"] = 1;
            $value["industryid"] = $value["id"];
            unset($value["id"]);
            $temp = explode("A", $value["backCall"]);
//                    var_dump($temp);
            $value["backCall"] = $temp["0"];
            $value["phone1"] = $value["backCall"];
            unset($value["backCall"]);
        }
        var_dump($data);
        die();
        $agent = M('company', 'vtiger_', 'mysql://root:anlaigz@192.168.200.144:3306/crm_default');
//                $dd = $agent->where("1")->select();
//                var_dump($dd);
        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
//                    var_dump($data[$i]);
            $id = $agent->where("company = '" . $data[$i]["company"] . "'")->getField("id");
            if ($id > 0) {
                echo '修改';
                $agent->where("id=" . $id)->save($data[$i]);
            } else {
                echo '增加';
                $agent->add($data[$i]);
            }
            unset($data[$i]);
        }
    }

    /**
     * 定时计划的第二步,对数据进行分析，生成各种ID号码
     */
    function step1() {
        $gb = M("Guestbook");
        $project = M("project");
        $aList = $gb->where("site =''")->field("ids,address")->limit(20000)->select();
        $count = count($aList);
        for ($i = 0; $i <= $count; $i++) {
            echo $aList[$i]["ids"] . "\n";
            if (substr($aList[$i]["address"], 0, 4) == "http") {
                $aUrl = parse_url(trim($aList[$i]["address"]));
            } else {
                $aUrl = parse_url(trim("http://" . $aList[$i]["address"]));
            }
            echo $aUrl["host"] . '<br />';
            if (in_array($aUrl["host"], C("ls"))) {
                if ($aUrl["host"] == "wap.liansuo.com") {
                    $aPath = explode("/", $aUrl["path"]);
                    $iPID = $aPath[3];
                } else {
                    $aPath = explode("/", $aUrl["path"]);
                    $iPID = str_replace(".html", "", $aPath[3]);
                }
                $site = "ls";
            } elseif (in_array($aUrl["host"], C("zf"))) {
                $iPID = substr($aUrl["query"], strpos($aUrl["query"], "=") + 1);
                $flag = is_numeric($iPID);
                if (!$flag) {
                    $path = str_replace("/", "", $aUrl["path"]);
                    $path = str_replace(".html", "", $path);
                    $pathdata = explode("-", $path);
                    $iPID = $pathdata[0];
                }
                $site = "zf";
            } elseif (in_array($aUrl["host"], C("WP"))) {
                $webpage = $aUrl["path"];
                $webpage = str_replace("/wp", "/ws", $webpage);
                $where['webPage'] = array('like', '%' . $webpage . '%');
                $where['site'] = array('egt', 'wp');
                $projectID = $project->where($where)->getField("projectID");
                echo $project->getLastSql();
                var_dump($projectID);
                $iPID = $projectID;
                $site = "wp";
            } elseif (in_array($aUrl["host"], C("jm"))) {
                $content = file_get_contents($aList[$i]["address"]);
                ;
                preg_match_all('/<input.*?name="p" value="(\d+)"/', $content, $matches);
                $iPID = $matches[1][0];
                $site = "91";
            } else {
                $iPID = str_replace(".html", "", substr($aUrl["path"], strrpos($aUrl["path"], "_") + 1));
                $site = "28";
            };
            $aData["ids"] = $aList[$i]["ids"];
            $aData["project_id"] = intval($iPID);
            $aData["site"] = $site;
            //如果不是致富网数据，就表示已经经过查重了
            if ($site != 'zf') {
                $aData["repeat_check"] = 1;
            }
            $gb->save($aData);
            unset($aData);
        }
        $this->display("blank");
    }

    /**
     * 临时程序，更新deal_date
     */
    function temp() {
        $gb = M("Guestbook");
        $aList = $gb->where("deal_date='0000-00-00' AND deal_time <>'0000-00-00 00:00:00'")->limit(1000)->getField("ids,deal_time");
        $i = 0;
        foreach ($aList as $k => $r) {
            $a = array();
            //$a["ids"] = $k;
            //$aData['deal_date'] = date("Y-m-d", strtotime($r));
            //echo "UPDATE `guestbook` SET `deal_date`='".date("Y-m-d", strtotime($r))."' WHERE ( ids=$k )" ;
            $gb->execute("UPDATE `guestbook` SET `deal_date`='" . date("Y-m-d", strtotime($r)) . "' WHERE ( ids=$k ) ");
            //$ss = $gb->where("ids=".$k)->data($aData)->save();
            echo $i . "--" . $k . "---" . $r . "--" . $aData["deal_date"];
            $i++;
        }
        $this->display("index");
    }

}
