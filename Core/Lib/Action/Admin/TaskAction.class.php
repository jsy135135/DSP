<?php

/**
 * 自动处理等相关
 *
 */
class TaskAction extends Action {
    public function sys_toCCenter(){
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
    public function sys_toCallCenter(){
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
        $url = "//3w.1552828.com/wp/xzxlz/index.htm";
        $urldata = parse_url($url);
        var_dump($urldata);
    }

    /**
     * 定时计划的第二步,对数据进行分析，生成各种ID号码
     */
    function step1() {
        $gb = M("Guestbook");
        $project = M("project");
        $aList = $gb->where("site =''")->field("ids,address")->limit(20000)->select();
//                        $aList = $gb->where("project_id =0 AND add_date = '2014-09-22'")->field("ids,address")->limit(20000)->select();
        $count = count($aList);
//                        $count = 3;
//                        var_dump($aList);
        for ($i = 0; $i <= $count; $i++) {
            echo $aList[$i]["ids"] . "\n";
            if (substr($aList[$i]["address"], 0, 4) == "http")
                $aUrl = parse_url(trim($aList[$i]["address"]));
            else
                $aUrl = parse_url(trim("http://" . $aList[$i]["address"]));
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
            } else {
                $iPID = str_replace(".html", "", substr($aUrl["path"], strrpos($aUrl["path"], "_") + 1));
                $site = "28";
            }
            $aData["ids"] = $aList[$i]["ids"];
            $aData["project_id"] = intval($iPID);
            $aData["site"] = $site;
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
     * 每天设置发送状态为1，表示可以接受,每天执行一次，而且只能执行一次
     */
    public function setSendStatusByDay() {
        $p = M("Project");
        $iStatus = $p->where("status > 0")->setField("sendStatus", 1);
        return $iStatus;
    }

}
