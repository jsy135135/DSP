<?php

//输出header头

header("content-type:text/html;charset=utf-8");

//客户信息的处理

class GuestbookNAction extends OQAction {

    //一次外呼的数据展示



    function index() {

        //判断是否存在session

        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }

        $uID = session("username");
        if ($uID == "admin")
            $uID = 805; //默认外呼标记为826

        $dDate = date("Y-m-d", strtotime("10 days ago"));  //昨天1 days ago
        $gb = M("guestbook");
        $aList = $gb->where("deal_status = 1 AND project_id > 0 AND u_id= " . $uID . " AND update_date>='" . $dDate . "' AND deal_time='0000-00-00 00:00:00' AND repeat_phone = 0")->order("Thetime desc")->select();

        $aListcount = count($aList);

        for ($i = 0; $i < $aListcount; $i++) {

            $str_re = array(
                'ndn_$t2/' => 'ndn_$t2_ding/',
                'ndn_$t2/' => 'ndn_$t2_ding/',
                'xyq_$t2/' => 'xyq_$t2_ding/',
                '100dufei_$t2/' => '100dufei_$t2_ding/',
                'miaoge_$t2/' => 'miaoge_$t2_ding/',
                'yknm_$t2/' => 'yknm_$t2_ding/',
                'lwx_$t2/' => 'lwx_$t2_ding/'
            );

            $aList[$i]['address'] = strtr($aList[$i]['address'], $str_re);
        }

        #加入推荐的栏目列表

        $aBigList = $this->_listCategory(1);

        $subList = D("Category")->where("pid != 0")->select();
//        $PoArray = $this->Poajax(1);
        #栏目列表传到页面

        $this->assign("aBigList", $aBigList);

        $this->assign("subList", $subList);

        $this->assign("uID", $uID);

        $this->assign("aList", $aList);
        $this->assign("PoArray",$PoArray);
        $this->display("indexn");
    }

    /*
     * 绩效ajax
     */

    public function Poajax($type=0) {
        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }
        $uID = session("username");
        if ($uID == "admin")
            $uID = 805; //默认外呼标记为826
        $tDate = date('Y-m-d'); //当天时间
        if ($tDate > date("Y-m-26") && $tDate <= date("Y-m-t")) {
            $startdate = date('Y-m-27'); //本月27号
            $enddate = date("Y-m-t"); //本月最后一天
        } else {
            $startdate = date('Y-m-27', strtotime('-1 month')); //上月27号
            $enddate = date("Y-m-26"); //本月26号
        }
        $sSQL = "addDate >= '" . $startdate . "' AND addDate <= '" . $enddate . "'";
        $dealed = M("data_dealed");
        $again = M("data_again");
        $user = M("user");

        $aUserList = $dealed->where($sSQL . "AND u_id <> 0 AND u_id <>10086")->field(" DISTINCT u_id as uid")->order('u_id asc')->select();
        $aUserListcount = count($aUserList) - 1;

        for ($j = 0; $j <= $aUserListcount; $j++) {
            $i = $aUserList[$j]["uid"];
            $row[$i]["uid"] = $i;
            $row[$i]["username"] = $user->where("username=" . $i)->getField("remark");
            $row[$i]["firstSendOKNum"] = $dealed->where($sSQL . "AND u_id=$i AND status>0")->count("DISTINCT phone"); //首次提交成功数
            $data[$j] = $row[$i]["firstSendOKNum"];
        }
        $MaxNum = max($data); //最大提交数
        $PoNum = $dealed->where($sSQL . "AND u_id = '" . $uID . "' AND status>0")->count("DISTINCT phone"); //个人提交数
        if ($MaxNum <= 700) {
            $Money = 0;
        } elseif ($MaxNum > 700 && $MaxNum < 800) {
            $Money = ($MaxNum - 700) * 1.5;
        } elseif ($MaxNum >= 800) {
            $Money = ($MaxNum - 700) * 4;
        }
        if ($PoNum <= 700) {
            $PMoney = 0;
        } elseif ($PoNum > 700 && $PoNum < 800) {
            $PMoney = ($PoNum - 700) * 1.5;
        } elseif ($PoNum >= 800) {
            $PMoney = ($PoNum - 700) * 4;
        }
        $PoArray = array(
            'MaxNum' => $MaxNum,
            "Money" => $Money,
            "PoNum" => $PoNum,
            "PMoney" => $PMoney,
        );
        if($type>0){
                 return $PoArray;   
        }else{
            echo json_encode($PoArray);
        }
    }

    /**

     * 分类列表

     */
    private function _listCategory($bIsBig = 0) {

        if ($bIsBig)
            $aList = D("Category")->where("pid=0")->select();
        else
            $aList = D("Category")->select();

        return $aList;
    }

    /**

     * 二次处理数据

     */
    function callAgain() {

        $uID = session("username");

        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826

        $dDate = $_REQUEST["date"] == "" ? date("Y-m-d") : $_REQUEST["date"];

        $gb = M("guestbook");

        //所有的处理过，但返回状态不为0和7的，7表示不需要

        $iNoAssignCount = $gb->where("u_id=$uID AND deal_status not in (0,1,7,8) AND  add_date='" . $dDate . "' AND again=1 AND send_status ='' AND repeat_phone = 0")->count();

        $aList = $gb->where("u_id=$uID AND deal_status not in (0,1,7,8) AND  add_date='" . $dDate . "' AND again=1 AND send_status ='' AND repeat_phone = 0")->order(" ids asc ")->limit(100)->select();

        $aBigList = $this->_listCategory(1);

        $subList = D("Category")->where("pid != 0")->select();

        #栏目列表传到页面

        $this->assign("aBigList", $aBigList);

        $this->assign("subList", $subList);

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

        $lenth = strlen($sMobile);

        if ($lenth < 11) {

            $aInfo["ips"] = '';

            $aInfo["project_id"] = '';

            $aInfo["site"] = '';

            $aInfo["ids"] = '';

            echo json_encode(array($aInfo["ips"], $aInfo["project_id"], $aInfo["site"], $aInfo["ids"]));

            exit();
        }

        $gb = M("guestbook");

        $aInfo = $gb->where("phone = '$sMobile'")->limit(1)->order("ids desc")->find();

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

        #接收提交过来的数据，进入到审核数据库

        $d = M("DataDealed");

        $p = M("project");

        $g = M("guestbook");

        //进行参数的接收，并且进入到待发送数据库中，进行字段的标注

        $rs = $p->where("projectID = '" . $_REQUEST['projectID'] . "' AND level  >=4 AND status > 0 AND sendStatus > 0")->select();

        if ($rs == null) {

            echo '所提交的项目不在项目列表中，不接受推送';

            exit;
        }

        if ($rs[0]['numbers'] <= 0) {

            echo '此项目已经达到推送量';

            exit;
        }

        $aD = array();

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

        //如果前端没有传输过来g_id的话，就去查g_id

        if (!empty($_REQUEST['guestbook_id'])) {

            $aD["g_id"] = $_REQUEST["guestbook_id"];
        } else {

            $aD["g_id"] = $g->where("phone = '" . $aD['phone'] . "' AND site = " . $aD['site'] . " AND project_id = '" . $aD['projectID'] . "'")->getField("ids");
        }

        if ($aD["g_id"] == NULL) {

            $aD["g_id"] = 0;
        }

        $d->add($aD);

        $p->where("projectID= " . $_REQUEST['projectID'] . " AND site = '" . $_REQUEST['website'] . "'")->setDec('numbers');

        $numbers = $p->where("projectID= " . $_REQUEST['projectID'] . " AND site = '" . $_REQUEST['website'] . "'")->getField("numbers");

        //在进行还需量的查询，进行相关发送状态的更改

        if ($numbers <= 0) {

            $update_sendstatus = $p->where("projectid = '" . $projectID . "' AND site = '" . $site . "'")->setField("sendStatus", '0');
        }

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

     * 连锁第三方转接传值标注

     * Time:2015年7月29日13:18:00

     * By:siyuan

     */

    public function sanfang() {

        $p = M("project");

        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }

        $uID = session("username");

        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826

        $data_dealed = M("data_dealed");

        $guestbook = M("guestbook");

        $aData = array();

        $gData = array();

        #data_dealed表增加数据

        $aData["g_id"] = $_REQUEST["guestbook_id"];

        $aData["name"] = $_REQUEST["username"];

        $aData["content"] = $_REQUEST["content"];

        $aData["phone"] = $_REQUEST["phone"];

        $aData["address"] = $_REQUEST["address"];

        $aData["domain"] = $_REQUEST["website"];

        $aData["projectID"] = $_REQUEST["projectID"];

        $aData["site"] = $_REQUEST["website"];

        $aData["ip"] = $_REQUEST["ip"];

        $aData["addDate"] = date("Y-m-d");

        $aData["Thetime"] = date("Y-m-d H:i:s");

        $aData["u_id"] = $uID;

        $aData["check"] = 0;

        $aData["regular"] = 0;

        //在guestbook表进行备注

        $gData["ids"] = $_REQUEST["guestbook_id"];

        $gData["deal_time"] = date("Y-m-d H:i:s");

        $gData["deal_date"] = date("Y-m-d");

        $gData["deal_status"] = 80; //在G表标注已经转接的号码

        $aData["status"] = 0;

        $aData["transfer"] = 2;

        //实时标注转接量

        $p->where("projectID= " . $_REQUEST['projectID'] . " AND site = '" . $_REQUEST['website'] . "'")->setDec('numbers');

        $drs = $data_dealed->add($aData);

        $grs = $guestbook->save($gData);

        echo $drs . $grs;
    }

    /*

     * 前端ajax传值

     * 收集和标注转接过的电话的信息，并入库保存

     * Time:2014/09/18 14:45:50

     * By:siyuan

     */

    public function transfer() {

        $p = M("project");

        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }

        $uID = session("username");

        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826

        $data_dealed = M("data_dealed");

        $guestbook = M("guestbook");

        $aData = array();

        $gData = array();

        #data_dealed表增加数据

        $aData["g_id"] = $_REQUEST["guestbook_id"];

        $aData["name"] = $_REQUEST["username"];

        $aData["content"] = $_REQUEST["content"];

        $aData["phone"] = $_REQUEST["phone"];

        $aData["address"] = $_REQUEST["address"];

        $aData["domain"] = $_REQUEST["website"];

        $aData["projectID"] = $_REQUEST["projectID"];

        $aData["site"] = $_REQUEST["website"];

        $aData["ip"] = $_REQUEST["ip"];

        $aData["addDate"] = date("Y-m-d");

        $aData["Thetime"] = date("Y-m-d H:i:s");

        $aData["u_id"] = $uID;

        $aData["check"] = 0;

        $aData["regular"] = 0;

        //在guestbook表进行备注

        $gData["ids"] = $_REQUEST["guestbook_id"];

        $gData["deal_time"] = date("Y-m-d H:i:s");

        $gData["deal_date"] = date("Y-m-d");

        $gData["deal_status"] = 8; //在G表标注已经转接的号码

        $aData["status"] = 0;

        $aData["transfer"] = 1;

        //实时标注转接量

        $p->where("projectID= " . $_REQUEST['projectID'] . " AND site = '" . $_REQUEST['website'] . "'")->setDec('numbers');

        $drs = $data_dealed->add($aData);

        $grs = $guestbook->save($gData);

        echo $drs . $grs;
    }

    /*

     * 对数据进行审核检查

     * Time:2014/08/01 10:36

     * By siyuan

     */

    function check() {

        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }

        $rolecheck = M("user")->where("username = '" . $_SESSION['username'] . "'")->getField("role");

        $startdate = $_REQUEST["startdate"] == "" ? date("Y-m-d") : $_REQUEST["startdate"];

        $enddate = $_REQUEST["enddate"] == "" ? date("Y-m-d") : $_REQUEST["enddate"];

        $dsql = "addDate >= '" . $startdate . "' AND addDate <= '" . $enddate . "'";

        $d = M("DataDealed");

        $act = $_REQUEST["act"] == "" ? 1 : $_REQUEST["act"];

        if ($act == 1) {

            if ($_SESSION['username'] == '10086') {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id = 10086  AND u_id<>0 order by u_id asc");
            } else {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id<>0 AND u_id<>10086 AND d.check = 0 AND d.regular = 0 order by u_id asc");
            }
        } elseif ($act == 2) {

            if ($_SESSION['username'] == '10086') {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id = 10086  AND u_id<>0 order by u_id asc");
            } else {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id<>0 AND u_id<>10086 AND d.check = 1 order by u_id asc");
            }
        } elseif ($act == 3) {

            if ($_SESSION['username'] == '10086') {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id = 10086  AND u_id<>0 order by u_id asc");
            } else {

                $datalist = $d->query("select p.name as projectname ,d.* from data_dealed as d left join project as p on d.projectID = p.projectID AND d.site = p.site where " . $dsql . " AND u_id<>0 AND u_id<>10086 order by u_id asc");
            }
        }

        $datalistcount = count($datalist);

        #替换，输出数据的类型

        for ($i = 0; $i < $datalistcount; $i++) {

            if ($datalist[$i]["transfer"] == 0) {

                $datalist[$i]["transfer"] = '留言';
            } elseif ($datalist[$i]["transfer"] == 2) {

                $datalist[$i]["transfer"] == '三方转接';
            } else {

                $datalist[$i]["transfer"] = '转接';
            }
        }

        $datalist = json_encode($datalist);

        $this->assign('rolecheck', $rolecheck);

        $this->assign('act', $act);

        $this->assign('startdate', $startdate);

        $this->assign("enddate", $enddate);

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

        $SendData = D("SendData");

        if (!isset($_SESSION['username'])) {

            session(null);

            redirect(C('cms_admin') . '?s=Admin/Login');
        }

        $checker = session("username");

        $d = M("data_dealed");

        $p = M("project");

        $id = $_POST["id"];

        $projectID = $_POST["projectID"];

        $site = $_POST["site"];

        $value = $_POST["data"];

        $Check = $d->where("id=$id")->getField("check");

        if ($Check == 1) {

            echo '此数据已经审核过了,请勿重复审核！！！';

            exit;
        }

        if ($value == 0) {

            $newdata = array();

            $newdata['check'] = 1;

            $newdata['regular'] = 0;

            $newdata['checker'] = $checker;

            $d->where("id=$id")->save($newdata);

            $rt = $p->where("projectID = " . $projectID . " AND site = '" . $site . "'")->setInc('numbers');

            echo '此数据被审核为无效';

            exit;
        }

        $AData = $d->where("id = $id")->select();

        $AData = $AData[0];

//对转接的数据进行特殊的处理

        if ($AData["transfer"] == 1) {

            //判断转接网站进行处理

            $newdata = array();

            $aData = array();

            $aData["user_name"] = $AData["name"];

            $aData["project_id"] = $AData["projectID"];

            $aData["phone"] = $AData["phone"];

            $aData["content"] = $AData["content"];

            $aData["ips"] = $AData["ip"];

            $aData["address"] = $AData["address"];

            $sSite = $AData["site"];

            if ($AData["site"] == '28') {

                $newdata['status'] = 8;

                $lstatus = 8;
            } elseif ($AData["site"] == 'ls') {

                $newdata['status'] = 8;

                $lstatus = $newdata['status'];
            } elseif ($AData["site"] == 'zf') {

                $newdata['status'] = $this->sendToZF_t($aData);

                $lstatus = $newdata['status'];
            }



            $newdata['check'] = 1;

            $newdata['regular'] = $value;

            $d->where("id = $id")->save($newdata);

            echo '此数据被审核有效' . ' 转接网站为：' . $AData["site"] . '<br />返回状态为：' . $lstatus;

            exit();
        }

        $aData = array();

        $aData["user_name"] = $AData["name"];

        $aData["project_id"] = $AData["projectID"];

        $aData["phone"] = $AData["phone"];

        $aData["content"] = $AData["content"];

        $aData["ips"] = $AData["ip"];

        $aData["address"] = $AData["address"];

        $sSite = $AData["site"];

        if ($sSite == "ls") {

            $sSiteName = "ls";

            $aData["uid"] = $AData["u_id"];

            if ($AData["transfer"] == 2) {

                //以连锁新给的接口发送(三方转接标注)

                $iReturnID = $SendData->sendToLS($aData, 1);
            } else {

                $iReturnID = $SendData->sendToLS($aData);
            }
        } elseif ($sSite == "zf") {

            $sSiteName = "zf";

            $iReturnID = $SendData->sendToZf($aData);
        } elseif ($sSite == "wp") {

            $sSiteName = "wp";

            $iReturnID = $SendData->sendTowp($aData);
        } elseif ($sSite == "28") {

            $sSiteName = "28";

            $iReturnID = 8080;
        } else {

            $sSiteName = "91";

            $iReturnID = $SendData->sendTo91($aData);
        }

        $newdata = array();

        $newdata['status'] = $iReturnID;

        $newdata['check'] = 1;

        $newdata['regular'] = $value;

        $newdata['checker'] = $checker;

        $d->where("id = $id")->save($newdata);

        //更新项目的状态，如果返回值为负值的话，更新发送状态为0，表示不能发送
        //如果返回数是负数，进行项目状态的更改

        if ($iReturnID < 0 && $iReturnID != '-11') {

            //项目numbers字段+1

            $p->where("projectID = " . $projectID . " AND site = '" . $site . "'")->setInc('numbers');

            //项目发送状态置为不可发送，状态值为0

            $p->where("projectID=" . $projectID . " AND site = '" . $site . "'")->setField("sendStatus", "0");
        }

        echo '此数据被审核有效' . ' 推送网站为：' . $sSite . "\n" . '返回数为：' . $iReturnID;
    }

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

        if ($iCount <= 1000 && date("Y-m-d") == $dDate) {

            $aInfo = D("DataDealed")->where(" status <0 AND again=0")->order(" RAND()")->find(); //随机取一条数据吧@

            var_dump($aInfo);

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
