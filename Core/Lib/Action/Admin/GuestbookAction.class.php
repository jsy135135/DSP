<?php

//输出header头
header("content-type:text/html;charset=utf-8");

//客户信息的处理
class GuestbookAction extends OQAction {
    #一次外呼的数据展示

    function index() {
        //判断是否存在session
        if (!isset($_SESSION['username'])) {
            session(null);
            redirect(C('cms_admin') . '?s=Admin/Login');
        }
        $uID = session("username");
        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826
        $dDate = date("Y-m-d", strtotime("2 days ago"));  //昨天1 days ago
//		$TheDate = date("Y-m-d");
//                $Thetime = date("Y-m-d H:i:s");
        $gb = M("guestbook");
        $aList = $gb->where("deal_status = 1 AND project_id > 0 AND u_id= " . $uID . " AND add_date>='" . $dDate . "' AND deal_time='0000-00-00 00:00:00'")->order("ids desc")->select();
        $this->assign("uID", $uID);
        $this->assign("aList", $aList);
        $this->display("indexn");
    }

    /**
     * 二次处理数据
     */
    function callAgain() {
        $uID = session("username");
        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826       
//$dDate = date("Y-m-d", strtotime("2 days ago"));  //昨天1 days ago
        $dDate = $_REQUEST["date"] == "" ? date("Y-m-d") : $_REQUEST["date"];
        // $dDate = "2014-05-01";
        $gb = M("guestbook");
        //所有的处理过，但返回状态不为0和7的，7表示不需要
        $iNoAssignCount = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date='" . $dDate . "' AND again=1 AND send_status =''")->count();
        //if($iNoAssignCount > 0)
        $aList = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date='" . $dDate . "' AND again=1 AND send_status =''")->order(" ids asc ")->limit(100)->select();
        //else {
        //$gb->where("deal_status<>0 and deal_status <>7 AND  add_date<='".$dDate."' AND again=0 AND send_status<0")->limit(100)->setField("u_id",$uID);
        //$aList = $gb->where("u_id=$uID AND deal_status<>0 and deal_status <>7 AND  add_date>='".$dDate."' AND again=0 AND  send_status =''")->limit(100)->select();
        //}
        $this->assign("dDate", $dDate);
        $this->assign("uID", $uID);
        $this->assign("iNoAssignCount", $iNoAssignCount);
        $this->assign("nowAssignNum", $iNowNum);
        $this->assign("aList", $aList);
        $this->display();
    }

    /*
     * 获取到单个项目的抓取数量
     */

    function getNumByPID($iPID, $dDate, $sSite = "28") {
        $gb = M("Guestbook");
        $iCount = $gb->where("project_id=" . $iPID . " AND site='" . $sSite . "' AND add_date='" . $dDate . "'")->count();
        return $iCount;
    }

    /**
     * 单独提交留言的form
     */
    function gbForm() {
        $this->assign("isHF", $_REQUEST["hefei"]);
        $this->display();
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
        echo "完毕";
        // $this->display("");
    }

    /**
     * 获取今天发送留言并且返回状态为负值的数据
     * @return Ambigous <mixed, NULL, multitype:Ambigous <unknown, string> unknown , multitype:>
     */
    function getDenyProjectList() {
        $gb = M("Guestbook");
        $sBeginTime = date("Y-m-d") . " 00:00:00";
        $sEndTime = date("Y-m-d") . " 23:59:59";
        $aList = $gb->where("deal_time BETWEEN '" . $sBeginTime . "' AND '" . $sEndTime . "' AND send_status < 0")->select();
        //此处用select方法，主要是getField方法没办法用好
        return $aList;
    }

    /**
     * 根据电话号码选择信息
     */
    function getByMobile() {
        $sMobile = $_REQUEST["mobile"];
//                $sMobile =18091386259;
        $gb = M("guestbook");
        $aInfo = $gb->where("phone = '$sMobile'")->limit(1)->order("ids desc")->find();
//                echo '<pre>';
//                var_dump($aInfo);
        $sUrl = $aInfo["address"];
        if (substr($sUrl, 0, 4) <> "http")
            $sUrl = "http://" . $sUrl;
        $aUrl = parse_url($sUrl);

        if (in_array($aUrl["host"], C("ls"))) {
            if ($aUrl["host"] == "wap.liansuo.com") {
                $aPath = explode("/", $aUrl["path"]);
                $iPID = $aPath[3];
            } else {
                $aPath = explode("/", $aUrl["path"]);
                $iPID = str_replace(".html", "", $aPath[3]);
            }
        } elseif (in_array($aUrl["host"], C("zf"))) {
            $iPID = substr($aUrl["query"], strpos($aUrl["query"], "=") + 1);
        } else {

            $iPID = str_replace(".html", "", substr($aUrl["path"], strrpos($aUrl["path"], "_") + 1));
        }
//			echo json_encode(array($aInfo["ips"],$iPID,$aUrl["host"]));
        echo json_encode(array($aInfo["ips"], $aInfo["project_id"], $aInfo["site"], $aInfo["ids"]));
    }

    /**
     * 处理url中的信息
     * @param unknown $sUrl
     */
    public function dealUrl() {
        $sUrl = $_REQUEST["url"];
        if (substr($sUrl, 0, 4) <> "http")
            $sUrl = "http://" . $sUrl;
        $aUrl = parse_url($sUrl);
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
            $site = "zf";
        } else {
            $iPID = str_replace(".html", "", substr($aUrl["path"], strrpos($aUrl["path"], "_") + 1));
            $site = "28";
        }
        echo json_encode(array($aUrl["host"], $iPID));
    }

    /**
     * 内部发送数据，发送新鲜数据
     * 先进行展示到审核列表中，然后再进行发送到北京方面
     * Time：2014/07/31 15:01
     * By siyuan
     */
    function sendDataByInter() {
        //判断是否存在session值,不存在重新登录
        if (!isset($_SESSION['username'])) {
            session(null);
            redirect(C('cms_admin') . '?s=Admin/Login');
        }
        //TP判断session是否设置
        //session('?username');
        #接收提交过来的数据，进入到审核数据库
        $d = M("DataDealed");
        $p = M("project");
        $g = M("guestbook");
//        $_REQUEST['projectID'] = 7329;
        //进行参数的接收，并且进入到待发送数据库中，进行字段的标注
        $rs = $p->where("projectID = '" . $_REQUEST['projectID'] . "' AND status > 0 AND sendStatus > 0")->select();
//        $rs = $p->where("projectID = 7329 AND status > 0 AND sendStatus > 0")->select();
//        var_dump($rs);
//        var_dump($rs[0]['numbers']);
//        die();
        if ($rs == null) {
            echo '所提交的项目不在项目列表中，不接受推送';
            exit;
        }
        if ($rs[0]['numbers'] <= 0) {
            echo '此项目已经达到推送量';
            exit;
        }
        $aD = array();
//        $aD["g_id"] = $_REQUEST["guestbook_id"];
        $aD["name"] = $_REQUEST["username"];
        $aD["phone"] = $_REQUEST["phone"];
        $aD["content"] = $_REQUEST["content"];
        $aD["address"] = $_REQUEST["address"];
        $aD["domain"] = $_REQUEST["website"];
        $aD["projectID"] = $_REQUEST["projectID"];
        $aD["site"] = $_REQUEST["website"];
        $aD["ip"] = $_REQUEST["ip"];
        $aD["status"] = 0;
        $aD["addDate"] = date("Y-m-d");
        $aD["u_id"] = session("username");
        $aD["Thetime"] = date("Y-m-d H:i:s");
        $aD["check"] = 0;
        $aD["regular"] = 0;
        $d->add($aD);
        $p->where("projectID= " . $_REQUEST['projectID'] . " AND site = '" . $_REQUEST['website'] . "'")->setDec('numbers');
//        var_dump($_REQUEST);
//        die();
        if ($_REQUEST["again"] == 1) {
            $aT = array();
            $aT["ids"] = $_REQUEST["guestbook_id"];
            $aT["again"] = 1;
            $g->save($aT);
        } else {
            $aData = array();
            $aData["deal_status"] = 0;
            $aData["ids"] = $_REQUEST["guestbook_id"];
            $aData["deal_time"] = date("Y-m-d H:i:s");
            $aData["deal_date"] = date("Y-m-d");
            $g->save($aData);
        }
        echo '数据提交成功，进入审核列表中~~~';
    }

    /*
     * 对数据进行审核检查
     * Time:2014/08/01 10:36
     * By siyuan
     */

    function check() {
        $date = date("Y-m-d");
//        $date = '2014-08-03';
        $d = M("DataDealed");
        $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where addDate = '" . $date . "' AND u_id<>10086 order by u_id asc");
        $datalistcount = count($datalist);
//        echo '<pre>';
//        var_dump($datalist);
        $this->assign('date', $date);
        $this->assign('count', $datalistcount);
        $this->assign('datalist', $datalist);
        $this->display();
    }

    /*
     * 对审核过的数据进行发送   
     * Time 2014/08/01 10:37
     * By siyuan
     */

    function sendToBJ() {
        $d = M("data_dealed");
        $p = M("project");
//        var_dump($_POST);
        $id = $_POST["id"];
        $projectID = $_POST["projectID"];
//        $name = $_POST["name"];
//        echo $projectID;
//        die();
        $site = $_POST["site"];
//        echo $id;
        $value = $_POST["data"];
//        var_dump($_POST);
//        die();
        $Check = $d->where("id=$id")->getField("check");
        if ($Check == 1) {
            echo '此数据已经审核过了,请勿重复审核！！！';
            exit;
        }
        if ($value == 0) {
            $newdata = array();
//            $newdata['name'] = $name;
            $newdata['check'] = 1;
            $newdata['regular'] = 0;
            $d->where("id=$id")->save($newdata);
//            $p->where("projectID= ".$_REQUEST['projectID']." AND site = '".$_REQUEST['website']."'")->setDec('numbers');
//            echo $projectID.$site;
//            die();
            $rt = $p->where("projectID = " . $projectID . " AND site = '" . $site . "'")->setInc('numbers');
//            var_dump($rt);
            echo '此数据被审核为无效';
//            die();
            exit;
        }
//        echo $id.' '.$value;
//        $id = 95547;
        $AData = $d->where("id = $id")->select();
        $AData = $AData[0];
        $aData = array();
        $aData["user_name"] = $AData["name"];
        $aData["project_id"] = $AData["projectID"];
        $aData["phone"] = $AData["phone"];
        $aData["content"] = $AData["content"];
        $aData["ips"] = $AData["ip"];
        $aData["address"] = $AData["address"];
        $sSite = $AData["site"];
//        var_dump($AData);
//        var_dump($aData);
//        var_dump($sSite);
//        die();
//        var_dump($AData);
        if ($sSite == "ls") {
            $sSiteName = "ls";
            $iReturnID = $this->sendToLS($aData);
        } elseif ($sSite == "zf") {
            $sSiteName = "zf";
            $iReturnID = $this->sendToZf($aData);
        } elseif ($sSite == "wp") {
            $sSiteName = "wp";
            $iReturnID = $this->sendTowp($aData);
        } else {
            $sSiteName = "28";
            $iReturnID = $this->sendTo28($aData);
        }
//         else {
//             $sSiteName = "91";
//             $iReturnID = $this->sendTo91($aData);
//         }
//        var_dump($iReturnID);
//        die();
        $newdata = array();
        $newdata['status'] = $iReturnID;
        $newdata['check'] = 1;
        $newdata['regular'] = $value;
//        $aaa = $d->where("id = $id")->select();
        $d->where("id = $id")->save($newdata);
//        $p->where("projectID = ".$projectID." AND site = '".$site."'")->setInc('numbers');
//        var_dump($aaa);
//                     var_dump($iReturnID);
//                $sSiteName = 'ls';
//                $iReturnID = '3306';
        //更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
        //如果返回数是负数，进行项目状态的更改
        if ($iReturnID < 0) {
            //项目numbers字段+1
            $p->where("projectID = " . $projectID . " AND site = '" . $site . "'")->setInc('numbers');
            //项目发送状态置为不可发送，状态值为0
            $p->where("projectID=" . $projectID . " AND site = '" . $site . "'")->setField("sendStatus", "0");
        }
        echo '此数据被审核有效' . ' 推送网站为：' . $sSite . "\n" . '返回数为：' . $iReturnID;
    }

    /**
     * 内部发送数据，发送新鲜数据
     */
//    function sendDataByInter() {
//        //判断是否存在session值,不存在重新登录
//        if (!isset($_SESSION['username'])) {
//            session(null);
//            redirect(C('cms_admin') . '?s=Admin/Login');
//        }
//        //TP判断session是否设置
//        //session('?username');
//        $p = M("project");
//        $aData = array();
//        $aData["user_name"] = $_REQUEST["username"];
//        $aData["project_id"] = $_REQUEST["projectID"];
//        $aData["phone"] = $_REQUEST["phone"];
//        $aData["content"] = $_REQUEST["content"];
//        $aData["ips"] = $_REQUEST["ip"];
//        $aData["address"] = $_REQUEST["address"];
//
//        $sSite = $_REQUEST["website"];
////                var_dump($aData);
////                die();
////                $numbers = $p->where("projectID = '".$aData["project_id"]."' AND site = $sSite")->getField("numbers");
////                if($numbers <= 0){
////                    echo '提交的项目，今天已经超过项目列表数量！';
////                    exit();
////                }
//        if ($sSite == "ls") {
//            $sSiteName = "ls";
//            $iReturnID = $this->sendToLS($aData);
//        } elseif ($sSite == "zf") {
//            $sSiteName = "zf";
//            $iReturnID = $this->sendToZf($aData);
//        } elseif ($sSite == "wp") {
//            $sSiteName = "wp";
//            $iReturnID = $this->sendTowp($aData);
//        } else {
//            $sSiteName = "28";
//            $iReturnID = $this->sendTo28($aData);
//        }
//        // else {
//        //     $sSiteName = "91";
//        //     $iReturnID = $this->sendTo91($aData);
//        // }
//
//            echo $sSiteName . "\n" . $iReturnID;
////                var_dump($iReturnID);
//        $aD = array();
//        //$aD["g_id"]			= $_REQUEST["guestbook_id"];
//        $aD["name"] = $_REQUEST["username"];
//        $aD["phone"] = $_REQUEST["phone"];
//        $aD["content"] = $_REQUEST["content"];
//        $aD["address"] = $_REQUEST["address"];
//        $aD["domain"] = $_REQUEST["website"];
//        $aD["projectID"] = $_REQUEST["projectID"];
//        $aD["site"] = $sSiteName;
//        $aD["ip"] = $_REQUEST["ip"];
//        $aD["status"] = $iReturnID;
//        $aD["addDate"] = date("Y-m-d");
//        $aD["u_id"] = session("username");
//        $aD["Thetime"] = date("Y-m-d H:i:s");
//        D("DataDealed")->add($aD);
//        if ($_REQUEST["again"] == 1) {
//            $aT["ids"] = $_REQUEST["guestbook_id"];
//            $aT["again"] = 1;
//            D("Guestbook")->save($aT);
//        } else {
//            $aData = array();
//            $aData["send_status"] = $iReturnID;
//            $aData["deal_status"] = 0;
//            $aData["ids"] = $_REQUEST["guestbook_id"];
//            $aData["deal_time"] = date("Y-m-d H:i:s");
//            $aData["deal_date"] = date("Y-m-d");
//            $guestbook = M("guestbook");
//            $guestbook->save($aData);
//        }
//        //更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
//        if ($iReturnID < 0) {
//            D("Project")->where("projectID=" . $_REQUEST["projectID"] . " AND site='" . $sSiteName . "'")->setField("sendStatus", "0");
//        }
//    }

    /**
     * 被拒绝的数据重新发送一次
     */
    function sendDataByDeny() {
        //判断是否存在session值,不存在重新登录
        if (!isset($_SESSION['username'])) {
            session(null);
            redirect(C('cms_admin') . '?s=Admin/Login');
        }
        //TP判断session是否设置
        //session('?username');
        $aData = array();
        $aData["user_name"] = $_REQUEST["username"];
        $aData["project_id"] = $_REQUEST["projectID"];
        $aData["phone"] = $_REQUEST["phone"];
        $aData["ips"] = $_REQUEST["ip"];
        $aData["address"] = $_REQUEST["address"];


        echo "28\n";
        $iReturnID = $this->sendTo28($aData);
        echo $iReturnID;

        $aD["data_id"] = $_REQUEST["id"];
        $aD["project_id"] = $_REQUEST["projectID"];
        $aD["site"] = $_REQUEST["website"];
        $aD["status"] = $iReturnID;
        $aD["add_date"] = date("Y-m-d");
        $aD["add_time"] = date("Y-m-d H:i:s");
        $aD["u_id"] = session("username");
        if ($aD["u_id"] <= 0)
            $aD["u_id"] = 100;
        $d = D("DataAgain");
        $d->add($aD);

        //修改本数据为已经处理
        $aT["id"] = $_REQUEST["id"];
        $aT["again"] = 1;
        D("DataDealed")->save($aT);

        //更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
        if ($iReturnID < 0) {
            D("Project")->where("projectID=" . $_REQUEST["projectID"] . " AND site='28'")->setField("sendStatus", "0");
        }
    }

    /**
     *
     * @param unknown $aData
     */
    function sendToLS() {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $client = new PHPRPC_Client ();
        $client->useService('http://www.liansuo.com/index.php?opt=gbinf'); //接口地址
        $aNewData = array(
            // 结构如下
            'typeofcontact' => 2, //1为留言2为400电话，直接触发企业电话
            'memberid' => $aData["project_id"], //项目id [必填]
            'trueName' => $aData["user_name"], //真是姓名[必填]
            'mobile' => $aData["phone"], //手机号码[必填]
            'ip' => $aData["ips"], //IP地址[必填]
//            'memberid' => 140, //项目id [必填]
//            'trueName' => 'siyuan', //真是姓名[必填]
//            'mobile' => '18535277952', //手机号码[必填]
//            'ip' => '192.168.200.55', //IP地址[必填]
        );
        $state_new = $client->clientSend($aNewData, 'utf-8', 'callfrommobile ', 'call$%^mobile');
        return $state_new;
//        var_dump($state_new);
    }

    /**
     *
     * @param unknown $aData
     */
    function sendTo28($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $rpc_client = new PHPRPC_Client();
        $rpc_client->setProxy(NULL);
        $rpc_client->useService('http://super.28.com/soap/server.php');  //接口地址
        $rpc_client->setKeyLength(1024);
        $rpc_client->setEncryptMode(3);

        // 推荐使用以上方法，可以减少出错的频率
        // 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
        $bData = array(
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );
        $state_new = $rpc_client->clientSend($bData, 'utf-8', 'xiancom', 'A342992b735');
        #查询项目相关的信息，$seat和$custid
        $p = M("project");
        $TelData = $p->where("projectID = '" . $aData["project_id"] . "' AND site='28'")->select();
        if ($TelData[0]["seat"] || $TelData[0]["custid"] == null)
            ; {
            echo '没有相关发送400电话的信息，不能发送';
        }
        $seat = $TelData[0]["seat"];
        $custid = $TelData[0]["custid"];
        $phone = $aData["phone"];
        $Tel400 = new ApiAction();
        $stu = $Tel400->tel28($seat, $custid, $phone);
        return $state_new;
    }

    /**
     *
     * @param unknown $aData
     */
    function sendToZf($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $rpc_client = new PHPRPC_Client();
        $rpc_client->setProxy(NULL);
        $rpc_client->useService('http://saas.zhifuwang.cn/soap/server1.php'); //接口地址
        //$rpc_client->setKeyLength(1024);
        //$rpc_client->setEncryptMode(3);
        // 推荐使用以上方法，可以减少出错的频率
        // 结构如下 telephone mobile可相同,email postcode可为空，时间请取准确时间
        $bData = array(
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );

        $state_new = $rpc_client->clientSend($bData, 'utf-8', 'liansuotozfw', 'zfwLianSuo2012@$^'); //向致富发送
        return $state_new;
    }

    /**
     * 推送到WP
     * Time:2014/07/30 16:39
     * By siyuan
     * 
     * 
     */
    function sendTowp($aData) {
        import("phprpc_client", "Core/Lib/Widget/", ".php");
        $bData = array(
            // 结构如下     
            'projectID' => $aData["project_id"],
            'trueName' => $aData["user_name"],
            'email' => '',
            'telephone' => $aData["phone"],
            'mobile' => $aData["phone"],
            'address' => $aData["address"],
            'postcode' => '',
            'addDate' => date("Y-m-d"),
            'addtime' => date("Y-m-d H:i:s"),
            'addHour' => date("H"),
            'content' => '对项目感兴趣' . $aData["content"],
            'ip' => $aData["ips"]
        );
//            var_dump($bData);
//        die();
//        'projectID' => 7375,
//        'trueName' => 'siyuan',
//        'email' => '',
//        'telephone' => 13354280969,
//        'mobile' => 13354280969,
//        'address' => '辽宁大连',
//        'postcode' => '',
//        'addDate' => date("Y-m-d"),
//        'addtime' => date("Y-m-d H:i:s"),
//        'addHour' => date("H"),
//        'content' => '对项目感兴趣' . $aData["content"],
//        'ip' => '192.168.200.55'
        $client = new PHPRPC_Client();
        $client->useService('http://super.wp28.com/soap/server.php'); //接口地址
        $state_new = $client->clientSend($bData, 'utf-8', 'dl_dsp', 'ef2084a02f69a8');
        return $state_new;
    }

    /**
     * 推送项目到91加盟网的项目
     *
     *
     */
//     public function sendTo91($aData) {
//         import("phprpc_client", "Core/Lib/Widget/", ".php");
//         $client = new PHPRPC_Client();
//         $client->setProxy(NULL);
// //                $client->useService('http://800.91jmw.com/index.php/welcome/pRpc');
//         $client->useService('http://192.168.200.61/kingboneguestbook/index.php/welcome/pRpc');
//         $client->setKeyLength(1000);
//         $client->setEncryptMode(3);
//         $client->setCharset('UTF-8');
//         $client->setTimeout(20);
//         $bData = array(
//             'p' => $aData["project_id"],
//             'name' => $aData["user_name"],
//             'mobile' => $aData["phone"],
//             'addr' => $aData["address"],
//             'content' => '对项目感兴趣' . $aData["content"]
// //                                'p' => 1,
// //				'name'	=> 'doudoumi',
// //				'email'		=> '',
// //				'mobile'	=> '13354260196',
// //				'address'	=> '大同',
// //				'content'	=> '对项目感兴趣，请联系我哈！'
//         );
//         var_dump($bData);
// //                die();
//         $Rarray = $client->hi('2014KB', $bData);
//         $state_new = $Rarray["result"];
//         return $Rarray;
// //                var_dump($state_new);
// //                return $state_new;
//     }

    /**
     *
     * @return number
     */
    function dealStatus() {
        if ($_GET["again"] == 1) {
            $aData = array();
            $aData["ids"] = $_GET["id"];
            $aData["again"] = 1;
            return D("Guestbook")->save($aData);
        } else {
            $aData = array();
            $aData["deal_status"] = $_GET["s"];
            $aData["ids"] = $_GET["id"];
            $aData["deal_time"] = date("Y-m-d H:i:s");
            $aData["deal_date"] = date("Y-m-d");
            $aData["again"] = 1;
            return D("Guestbook")->save($aData);
        }
    }

    function analysis() {
        $gb = M("guestbook");
        $iStartDate = strtotime("2013-12-08 00:00:00");
        $iEndDate = strtotime(date("Y-m-d H:i:s"));
        $j = 0;
        for ($i = $iEndDate; $i > $iStartDate;) {
            $dDate = date("Y-m-d", $i);
            $aList[$j]["date"] = $dDate;
            $aList[$j]["all"] = $gb->where("times LIKE '%$dDate%' ")->count("ids");
            $aList[$j]["28"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%28.com%'")->count("ids");
            $aList[$j]["13828"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%1382828.com%'")->count("ids");
            $aList[$j]["liansuo"] = $gb->where("times LIKE '%$dDate%' AND address LIKE  '%liansuo.com%'")->count("ids");
            $i = $i - 3600 * 24;
            $j++;
        }
        $this->assign("aList", $aList);
        $this->display();
    }

    /**
     * 系统定时自动发送数据到指定的服务器
     */
    function autoSendData() {
        $dDate = "2014-04-27";

        //先检查今天的发送数据量，如果数据超过1000，则什么也不执行
        $dealed = M("DataDealed");
        $iCount = $dealed->where(" addDate = '" . $dDate . "'")->count();
        //echo $iCount;
        if ($iCount <= 1000 && date("Y-m-d") == $dDate) {
            $aInfo = D("DataDealed")->where(" status <0 AND again=0")->order(" RAND()")->find(); //随机取一条数据吧@
            var_dump($aInfo);
            //$iNewProjectID = 7292;
            //获取新id的方法
            $pInfo = D("Project")->where(" projectID=" . $aInfo["projectID"] . " AND site='" . $aInfo["site"] . "'")->find();
            $iNewProjectID = D("Project")->where("pid=" . $pInfo["pid"] . " AND cid=" . $pInfo["cid"] . " AND status=1 AND sendStatus=1 AND projectID<>" . $aInfo["projectID"] . " AND site='28' ")->order(" RAND() ")->limit(1)->getField("projectID");
            $sPostStr = "id=" . $aInfo["id"] . "&username=" . $aInfo["name"] . "&projectID=" . $iNewProjectID . "&phone=" . $aInfo["phone"] . "&ip=" . $aInfo["ip"] . "&address=" . $aInfo["address"] . "&website=28";
            if ($iNewProjectID > 0)
                echo request_by_curl("http://192.168.200.52/tpcms/index.php?s=/Admin/Guestbook/sendDataByDeny", $sPostStr);
            else {
                $iNewProjectID = D("Project")->where("pid=" . $pInfo["pid"] . " AND status=1 AND sendStatus=1 AND projectID<>" . $aInfo["projectID"] . " AND site='28' ")->order(" RAND() ")->limit(1)->getField("projectID");
                $sPostStr = "id=" . $aInfo["id"] . "&username=" . $aInfo["name"] . "&projectID=" . $iNewProjectID . "&phone=" . $aInfo["phone"] . "&ip=" . $aInfo["ip"] . "&address=" . $aInfo["address"] . "&website=28";
                if ($iNewProjectID > 0)
                    echo request_by_curl("http://192.168.200.52/tpcms/index.php?s=/Admin/Guestbook/sendDataByDeny", $sPostStr);
            }
        }
        $this->display("blank");
    }

}