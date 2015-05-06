<?php

header("Content-type: text/html; charset=utf-8");

/**
 * 自动处理等相关
 *
 */
class TaskAction extends Action {
    /*
     * crm [客户关系管理系统]
     * Copyright (c) 2013
     * Author：siyuan
     * 模块名称：定时操作数据库，导入数据
     */
    #DSP数据特定项目的数据输出（此为介护宝项目）
    public function sysforycrm() {
        $guestbook = M("guestbook");
        $date = date("Y-m-d", strtotime("1 days ago"));
//        $date = '2014-12-28';
        $data = $guestbook->query("select ids,phone,add_date from guestbook where site = '91' AND project_id = 387 AND add_date = '".$date."'");
        $data = serialize($data);
        echo $data;
    }
    #进行前一天一堆多数据的输出
    public function sysfordcrm() {
        $guestbook = M("guestbook");
        $date = date("Y-m-d",strtotime("1 days ago"));
//        $dsql = "SELECT ids,phone,add_date FROM `guestbook` WHERE `add_date` = '".$date."' AND `project_id` = ''";
        $dsql = "SELECT ids,phone,add_date FROM guestbook WHERE add_date = '".$date."'AND project_id = '' AND ids >= ((SELECT MAX(ids) FROM guestbook)-(SELECT MIN(ids) FROM guestbook)) * RAND() + (SELECT MIN(ids) FROM guestbook)  LIMIT 400";
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

    function test() {
        $url = "http://wap.zft888.com/2276-3-1240.html?utm_source=Baidu";
        $urldata = parse_url($url);
        // var_dump($urldata);
        $path = str_replace("/", "", $urldata["path"]);
        $path = str_replace(".html", "", $path);
        $pathdata = explode("-", $path);
        // var_dump($pathdata);
        $iPID = $pathdata[0];
        // echo $projectID;
        // $iPID = substr($urldata["query"], strpos($urldata["query"], "=") + 1);
        var_dump($iPID);
        $value = is_numeric($iPID);
        var_dump($value);
        // var_dump($urldata);
    }

    function jsy() {
        $url = "http://800.quikio.cn/qmy/wap/?830";
        $content = file_get_contents($url);
        // <input type="hidden" name="p" value="301">
        preg_match_all('/<input type="hidden" name="p" value="(\d+)" \/>/', $content, $matches);
        $iPID = $matches[1][0];
        $site = "91";
    }

    /**
     * 定时计划的第二步,对数据进行分析，生成各种ID号码
     */
    function step1() {
        $gb = M("Guestbook");
        $project = M("project");
        $aList = $gb->where("site =''")->field("ids,address")->limit(20000)->select();
//        $aList = $gb->where("site =''")->field("ids,address")->limit(100000)->select();
//                        $aList = $gb->where("site = '' AND add_date >= '2015-03-26'")->field("ids,address")->limit(20000)->select();
        $count = count($aList);
//                        $count = 3;
//                        var_dump($aList);
        for ($i = 0; $i <= $count; $i++) {
            echo $aList[$i]["ids"] . "\n";
            if (substr($aList[$i]["address"], 0, 4) == "http") {
                $aUrl = parse_url(trim($aList[$i]["address"]));
            } else {
                $aUrl = parse_url(trim("http://" . $aList[$i]["address"]));
            }
//                                var_dump($aUrl);
            echo $aUrl["host"] . '<br />';
//                                $aUrl["host"] = '3w.wp28.com';
            if (in_array($aUrl["host"], C("ls"))) {
//                            echo 'ls'.'<br />';
                if ($aUrl["host"] == "wap.liansuo.com") {
                    $aPath = explode("/", $aUrl["path"]);
                    $iPID = $aPath[3];
                } else {
                    $aPath = explode("/", $aUrl["path"]);
                    $iPID = str_replace(".html", "", $aPath[3]);
                }
                $site = "ls";
            } elseif (in_array($aUrl["host"], C("zf"))) {
//                            echo 'zf'.'<br />';
                $iPID = substr($aUrl["query"], strpos($aUrl["query"], "=") + 1);
                $flag = is_numeric($iPID);
                if (!$flag) {
                    $path = str_replace("/", "", $aUrl["path"]);
                    $path = str_replace(".html", "", $path);
                    $pathdata = explode("-", $path);
                    // var_dump($pathdata);
                    $iPID = $pathdata[0];
                }
                $site = "zf";
            } elseif (in_array($aUrl["host"], C("WP"))) {
//                            echo 'wp'.'<br />';
//                            die();
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
                echo "i am 91 site";
                $content = file_get_contents($aList[$i]["address"]);
                // $content = file_get_contents($url);
                // <input type="hidden" name="p" value="301">
//                preg_match_all('/<input type="hidden" name="p" value="(\d+)" \/>/', $content, $matches);
                preg_match_all('/<input.*?name="p" value="(\d+)"/', $content, $matches);
                $iPID = $matches[1][0];
                $site = "91";
            } else {
                echo '不知道';
                $iPID = str_replace(".html", "", substr($aUrl["path"], strrpos($aUrl["path"], "_") + 1));
                $site = "28";
            }
            // var_dump(C("jm"));
            $aData["ids"] = $aList[$i]["ids"];
            $aData["project_id"] = intval($iPID);
            $aData["site"] = $site;
            //如果不是致富网数据，就表示已经经过查重了
            if($site != 'zf'){
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
    /***
     * 更新数据库里致富网的数据，通过查询接口，如果重复，进行标注
     * Time：2015年3月26日15:37:07
     * By:siyuan
     * 
     */
    function zf_repeat(){
        $date = date("Y-m-d");
        $g = M("guestbook");
        $data = $g->query("select ids,phone from guestbook where add_date = '".$date."' AND site = 'zf' AND repeat_check = 0 limit 20");
//var_dump($data);
        $datacount = count($data);
        $Api = new ApiAction();
        for($i=0;$i<$datacount;$i++){
           $repeat_phone = $Api->repeat_phone($data[$i]['phone']);
           $newdata['ids'] = $data[$i]['ids'];
           $newdata['repeat_check'] = 1;
           $newdata["repeat_phone"] = $repeat_phone;
           $rs = $g->save($newdata);
           var_dump($rs);
        }
//        var_dump($data);
    }
    /**
     * 同步项目信息
     * @param unknown $sSite
     */
    public function syncProject() {
        if ($this->_syncFrom28())
            echo "Sync 28 is ok<br/>";
        if ($this->_syncFromZF())
            echo "Sync zf is ok<br/>";
        if ($this->_syncFromLS())
            echo "Sync ls is ok<br/>";
//                        if($this->_syncFrom91())
//                                echo "Sync 91 is ok<br/>";
    }

    /**
     * 同步28的数据信息，直接同步，新数据插入，原来的数据直接更新。
     */
    private function _syncFrom28() {
        $p = M("Project");
        $aSource = xml2array(C("xml_28"));
        for ($i = 0; $i < count($aSource["root"]["items"]); $i++) {
            $aT = $aSource["root"]["items"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["clientID"] = $aT["clientID"];
                $aTemp["projectID"] = $aT["projectID"];
                if ($aT["statusStr"] == "空闲" || $aT["statusStr"] == "停用")
                    $aTemp["status"] = 0;
                else
                    $aTemp["status"] = 1;
                //$aTemp["status"] = $aT["statusStr"] == "空闲" ? 0:1;
                $aTemp["name"] = $aT["name"];
                $aTemp["webPage"] = $aT["adWebPage"];
                $aTemp["backCall"] = $aT["mobnum"];
                $aTemp["needNum"] = $aT["gbookNum"];
                $aTemp["numbers"] = $aT["gbookNum"];
                $aTemp["site"] = "28";
                $aTemp["catName"] = $aT["topCateName"];
                $aTemp["subCat"] = $aT["subCateName"];
                //如果数据已经存在，则更新数据，否则就是插入数据
                $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
                if ($id > 0)
                    $p->where("id=" . $id)->save($aTemp);
                else
                    $p->add($aTemp);
                unset($aTemp);
            }
        }
        return true;
    }

    /**
     * 同步致富网的信息
     */
    private function _syncFromZF() {
        $p = M("Project");
        $aSource = xml2array(C("xml_zf"));
        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
            $aT = $aSource["log"]["fields"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["clientID"] = 0;
                $aTemp["projectID"] = $aT["projectID"];
                $aTemp["status"] = 1;
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["web"];
                $aTemp["backCall"] = $aT["tel"];
                $aTemp["needNum"] = $aT["gbookNumMax"];
                $aTemp["numbers"] = $aT["gbookNumMax"];
                $aTemp["site"] = "zf";
                $aTemp["catName"] = $aT["catName"];
                $aTemp["subCat"] = $aT["catSubName"];
                //如果数据已经存在，则更新数据，否则就是插入数据
                $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
                if ($id > 0)
                    $p->where("id=" . $id)->save($aTemp);
                else
                    $p->add($aTemp);
                unset($aTemp);
            }
        }
        return true;
    }

    /**
     * 同步连锁网的信息
     */
    private function _syncFromLS() {
        $p = M("Project");
        $aSource = xml2array(C("xml_ls"));
        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
            $aT = $aSource["log"]["fields"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["clientID"] = 0;
                $aTemp["projectID"] = $aT["projectID"];
                $aTemp["status"] = 1;
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["adWebPage"];
                $aTemp["backCall"] = $aT["link"];
                switch ($aT["level"]) {
                    case "银牌会员":
                    case "VIP会员":
                        $iNeedNum = 3;
                        break;
                    case "按效果付费":
                    case "广告客户":
                        $iNeedNum = 5;
                        break;
                    default:
                        $iNeedNum = 2;
                        break;
                }
                $aTemp["needNum"] = $iNeedNum;
                $aTemp["site"] = "ls";
                $aTemp["catName"] = $aT["industry"];
                $aTemp["subCat"] = $aT["subindustry"];
                //如果数据已经存在，则更新数据，否则就是插入数据
                $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
                if ($id > 0)
                    $p->where("id=" . $id)->save($aTemp);
                else
                    $p->add($aTemp);
                unset($aTemp);
            }
        }
        return true;
    }

    /**
     * 同步91加盟网的信息
     */
    public function syncFrom91() {
        $aSource = file_get_contents(C("json_91"));
//                    var_dump($aSource);
        $aSource = json_decode($aSource, true);
        echo '<pre>';
        var_dump($aSource);
        header("content-type:text/html;charset=utf-8");
        $aSource = $aSource["data"];
        $aTemp = array();
        foreach ($aSource as $a => $val) {
            $aTemp["projectID"] = $a;
            $aTemp["name"] = $val;
            $aTemp["site"] = "91";
//                        var_dump($aTemp);
            $aTemp["status"] = 1;
            $aTemp["name"] = $aT["projectName"];
            $aTemp["webPage"] = $aT["web"];
            $aTemp["backCall"] = $aT["tel"];
            $aTemp["needNum"] = $aT["gbookNumMax"];
            $aTemp["numbers"] = $aT["gbookNumMax"];
            $aTemp["site"] = "zf";
            $aTemp["catName"] = $aT["catName"];
            $aTemp["subCat"] = $aT["catSubName"];
            //如果数据已经存在，则更新数据，否则就是插入数据
//                        $id = $p->where("clientID=".$aTemp["clientID"]." AND projectID=".$aTemp["projectID"]." AND site='".$aTemp["site"]."'")->getField("id");
//					if($id > 0)
//						$p->where("id=".$id)->save($aTemp);
//					else
//						$p->add($aTemp);
//					unset($aTemp);
        }
        return true;
    }

    /**
     *
     * 每天设置发送状态为1，表示可以接受,每天执行一次，而且只能执行一次
     */
    public function setSendStatusByDay() {
        $p = M("Project");
        $iStatus = $p->where("status > 0")->setField("sendStatus", 1);
        return $iStatus;
    }

}
