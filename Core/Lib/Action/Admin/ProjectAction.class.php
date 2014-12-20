<meta charset='utf-8' />
<?php

/*
 * 项目管理信息
 */

class ProjectAction extends Action {

    public function index() {
        //显示当前在使用的项目列表
//        var_dump($_REQUEST);
//        die();
        $act = ($_REQUEST["act"] == "") ? "1" : $_REQUEST["act"];
        $p = M("Project");
        $Data = M("data_dealed");
        $Sdata = M("data_again");
        $date = date("Y-m-d");
        if ($act == 1) {
            $aList = $p->where("status = 1 AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND level  >=4 order by site asc,catName asc,subCat asc");
//            var_dump($Asum);
        }
        // $aList = $p->where("1")->order('site asc,catName asc,subCat asc')->select();
        else {
            $aList = $p->where("status = 1 AND site = '" . $act . "' AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND site = '" . $act . "' AND level  >=4 order by site asc,catName asc,subCat asc");
//            var_dump($Asum);
        }
        foreach ($aList as &$r) {
//            if ($r["site"] == "28")
//                $r["http"] = "http://tj.28.com" . $r["webPage"];
//            else
            $r["http"] = $r["webPage"];
            if ($r["sendStatus"] == 0)
                $r["sendStatus"] = '否';
            else
                $r["sendStatus"] = '是';
            if ($r["status"] == 0)
                $r["status"] = '否';
            else
                $r["status"] = '是';
            if ($r["level"] == 5)
                $r["level"] = '按效果付费';
            if ($r["level"] == 4)
                $r["level"] = '广告客户';
        }
        $countaList = count($aList);
        for ($i = 0; $i < $countaList; $i++) {
            $aList["$i"]["alcount"] = ($aList["$i"]["needNum"] - $aList["$i"]["numbers"]);
        }
        $aCatList = D("category")->where(1)->getField("id,catname");
        $this->assign("countaList", $countaList);
        $this->assign("catList", $aCatList);
        $this->assign("aList", $aList);
//        var_dump($Asum);
        $Aalsum = $Asum[0]["Asum"];
        $Anumbers = $Asum[0]["Anumbers"];
        $Asends = ($Aalsum - $Anumbers);
        $Apersent = round($Asends / $Aalsum, 3) * 100;
        $this->assign("Aalsum", $Aalsum);
        $this->assign("Anumbers", $Anumbers);
        $this->assign("Asends", $Asends);
        $this->assign("Apersent", $Apersent);
        $BigList = $this->_listCategory(1);
        $SmallList = D("Category")->where("pid != 0")->select();
        $this->assign("BigList",$BigList);
//        var_dump($BigList);
//        var_dump($SmallList);
        $this->assign("SmallList",$SmallList);
        $this->display();
    }
    /*
     *Time:2014-11-14 14:55:18
     *定时同步之后，更新每个项目已经发送de数量
     *By:siyuan
     */
    public function dealtotel(){
        $p = M("project");
        $D = M("data_dealed");
        $d = M("data_again");
        $date = date("Y-m-d");
        $prolist = $p->where("status = 1 AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
        $prolistcount = count($prolist);
        for($i=0;$i<$prolistcount;$i++){
            $site = $prolist[$i]['site'];
            $projectID = $prolist[$i]['projectID'];
            $fristCount = $D->where("addDate = '" . $date . "'AND site = '" . $site . "' AND projectID = '" . $projectID . "' AND status >= 0 AND status <> 8")->count(); //此项目今日一次发送成功数
            $scendCount = $d->where("addDate = '" . $date . "'AND site = '" . $site . "' AND project_id = '" . $projectID . "' AND status >= 0 AND status <> 8")->count(); //此项目今天二次发送成功数
            $numbers = ($prolist["$i"]["needNum"] - $fristCount - $scendCount); //已经发送的数据量
            $rs = $p->where("projectid = '" . $projectID . "' AND site = '" . $site . "'")->setField("numbers", $numbers);
            if($rs){
                echo $site.'#'.$projectID.'#'.'succsess';
            }else{
                echo $site.'#'.$projectID.'#'.'enero';
            }
        }
    }
//    public function index() {
//        //显示当前在使用的项目列表
//        $act = ($_REQUEST["act"] == "") ? "1" : $_REQUEST["act"];
//        $p = M("Project");
//        $Data = M("data_dealed");
//        $Sdata = M("data_again");
//        $date = date("Y-m-d");
//        if ($act == 1)
//            $aList = $p->where("status = 1 AND site !='91' AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
//        // $aList = $p->where("1")->order('site asc,catName asc,subCat asc')->select();
//        else
//            $aList = $p->where("1")->select();
//        foreach ($aList as &$r) {
//            if ($r["site"] == "28")
//                $r["http"] = "http://tj.28.com" . $r["webPage"];
//            else
//                $r["http"] = $r["webPage"];
//            if ($r["sendStatus"] == 0)
//                $r["sendStatus"] = '否';
//            else
//                $r["sendStatus"] = '是';
//            if ($r["status"] == 0)
//                $r["status"] = '否';
//            else
//                $r["status"] = '是';
//        }
// //                        var_dump($aList);
//        $countaList = count($aList);
// //                        echo $countaList;
// //                        echo $site = $aList["1"]["site"];
//        for ($i = 0; $i < $countaList; $i++) {
//            $site = $aList["$i"]["site"];
//            $projectID = $aList["$i"]["projectID"];
//            $lastCount = $Data->where("addDate = '" . $date . "'AND site = '" . $site . "' AND projectID = '" . $projectID . "' AND status >= 0")->count(); //此项目今日一次发送成功数
// //            echo $lastCount;
//            $scendCount = $Sdata->where("addDate = '" . $date . "'AND site = '" . $site . "' AND project_id = '" . $projectID . "' AND status >= 0")->count(); //此项目今天二次发送成功数
// //            echo $scendCount;
//            $numbers = ($aList["$i"]["needNum"] - $lastCount - $scendCount);
// //            echo $numbers;
//            $aList["$i"]["alcount"] = ($lastCount + $scendCont);
//            $rs = $p->where("projectid = '" . $projectID . "' AND site = '" . $site . "'")->setField("numbers", $numbers);
// //            var_dump($rs);
//            $aList["$i"]{"numbers"} = $numbers;
// //                            var_dump($lastCount);
//        }
//        $aCatList = D("category")->where(1)->getField("id,catname");
// //                        var_dump($aList);
//        $this->assign("countaList", $countaList);
//        $this->assign("catList", $aCatList);
//        $this->assign("aList", $aList);
//        $this->display();
//    }

    /**
     * 项目分类的修改
     * */
    public function edit() {
        $id = $_REQUEST["id"];
        $one = D("Project")->where("id=$id")->select();
        $aBigList = $this->_listCategory(1);
        $alist = $aList = D("Category")->where("pid != 0")->select();
        $this->assign("one", $one);
        $this->assign("aBigList", $aBigList);
        $this->assign("aList", $aList);
        $this->display("edit");
    }

//    public function update() {
//        $aD = $_REQUEST;
////        echo 1;
////        die();
//        $id = $aD["id"];
//        $pid = $aD["pid"];
//        $cid = $aD["cid"];
//        $result = D("Project")->execute("update project set pid=" . $pid . ", cid=" . $cid . " where id=" . $id);
//        if ($result) {
//            $this->success("修改成功", U('/Admin/Project'));
//        } else {
//            $this->error("修改失败");
//        }
//    }
    /*
     * ajax修改分类
     * time:2014/09/09 12:03
     * By siyuan
     *
     */
    public function update() {
//        echo 11111;
//        var_dump($_REQUEST);
        $aD = $_REQUEST;
        $id = $aD["listid"];
        $pid = $aD["pid"];
        $cid = $aD["cid"];
        $result = D("Project")->execute("update project set pid=" . $pid . ", cid=" . $cid . " where id=" . $id);
//        if ($result) {
//            $this->success("修改成功", U('/Admin/Project'));
//        } else {
//            $this->error("修改失败");
//        }
    }

    public function indexJson() {
        $dDate = $_REQUEST["date"];
        if (empty($dDate))
            $dDate = date("Y-m-d", strtotime("1 days ago"));  //昨天
        $this->assign("date", $dDate);
        $this->display("indexJson");
    }

    public function showDetail() {
        $p = M("Project");
        $dDate = $_REQUEST["date"];
        $aList = $p->where("status=1")->select();
        foreach ($aList as &$r) {
            if ($r["site"] == "28")
                $r["http"] = "http://tj.28.com" . $r["webPage"];
            else
                $r["http"] = $r["webPage"];

            $r["count"] = R("Admin/Guestbook/getNumByPID", array($r["projectID"], $dDate, $r["site"]));
            $r["callOK"] = R("Admin/DataDealed/getNumByProject", array($r["projectID"], $dDate, $r["site"], "callOk"));
            $r["sendOK"] = R("Admin/DataDealed/getNumByProject", array($r["projectID"], $dDate, $r["site"], "sendOk"));
            $url = __ROOT__ . '/index.php/Admin/Project/denyAgain/pID/' . $r["projectID"] . '/site/' . $r["site"] . '/date/' . $dDate;
            $iNum = $r["callOK"] - $r["sendOK"];
            $r["denyNum"] = "<a href='" . $url . "' target='_blank'>" . $iNum . "</a>";

            $r["ratio_call"] = round($r["callOK"] / $r["count"], 2) * 100 . "%";
            $r["ratio_send"] = round($r["sendOK"] / $r["callOK"], 2) * 100 . "%";
        }
        echo json_encode($aList);
    }

    /**
     * 重新处理被拒绝的数据，按项目处理
     */
    public function denyAgain() {
        $iPID = $_REQUEST["pID"];
        $sSite = $_REQUEST["site"];
        $dDate = $_REQUEST["date"];
        //获取这个项目的信息
        $project = M("Project");
        $aInfo = $project->where("projectID=" . $iPID . " AND site='" . $sSite . "'")->find();
        if ($aInfo["site"] == "28")
            $aInfo["http"] = "http://tj.28.com" . $aInfo["webPage"];
        else
            $aInfo["http"] = $aInfo["webPage"];
        $dealed = A("DataDealed");
        $aList = R("Admin/DataDealed/getListByProject", array($iPID, $dDate, $sSite, "deny"));
        //选择这些被拒绝的数据，以及拒绝原因
        if ($_REQUEST["cat"] == "big")
            $sSearchCat = "catName";
        else
            $sSearchCat = "subCat";
        $aSendProjectList = $project->where(" $sSearchCat='" . $aInfo["subCat"] . "' AND sendStatus=1 AND status =1 AND site='28'")->order(" rand()")->limit(10)->select();
        if (count($aSendProjectList) == 0) { //该小分类没有信息
            $aSendProjectList = $project->where(" catName='" . $aInfo["catName"] . "' AND sendStatus=1 AND status =1  AND site='28'")->order(" rand()")->limit(10)->select();
        }
        if ($aInfo["site"] == "28") {
            $aInfo["http"] = "http://tj.28.com" . $aInfo["webPage"];
        } else
        $aInfo["http"] = $aInfo["webPage"];
        for ($i = 0; $i < count($aSendProjectList); $i++) {
            $aSendProjectList[$i]["http"] = "http://tj.28.com" . $aSendProjectList[$i]["webPage"];
        }
        $this->assign("date", $dDate);
        $this->assign("aProjectList", $aSendProjectList);
        $this->assign("aInfo", $aInfo);
        $this->assign("aList", $aList);
        $this->display("denyAgain");
    }

    /**
     * 重新发送已经处理过的数据，按留言处理
     */
    public function denyAgainByGB() {
        $id = $_REQUEST["id"];
        //获取这个留言的信息
        $project = D("Project");
        $aInfo = D("DataDealed")->where(" id=" . $id)->find();
        $pInfo = $project->where("projectID=" . $aInfo["projectID"] . " AND site='" . $aInfo["site"] . "'")->find();
        if ($pInfo["site"] == "28")
            $pInfo["http"] = "http://tj.28.com" . $pInfo["webPage"];
        else
            $pInfo["http"] = $pInfo["webPage"];

        if ($_REQUEST["cat"] == "big")
            $sSearchCat = "catName";
        else
            $sSearchCat = "subCat";
        $aSendProjectList = $project->where(" $sSearchCat='" . $pInfo["subCat"] . "' AND sendStatus=1 AND status =1 AND site='28'")->order(" rand()")->limit(10)->select();
        if (count($aSendProjectList) == 0) { //该小分类没有信息
            $aSendProjectList = $project->where(" catName='" . $pInfo["catName"] . "' AND sendStatus=1 AND status =1  AND site='28'")->order(" rand()")->limit(10)->select();
        }
        for ($i = 0; $i < count($aSendProjectList); $i++) {
            $aSendProjectList[$i]["http"] = "http://tj.28.com" . $aSendProjectList[$i]["webPage"];
        }
        $this->assign("date", $dDate);
        $this->assign("aProjectList", $aSendProjectList);
        $this->assign("aInfo", $aInfo);
        $this->assign("pInfo", $pInfo);
        $this->display("denyAgainByGB");
    }

    /**
     * 获取项目的发送状态
     * @return Ambigous <mixed, NULL, multitype:Ambigous <unknown, string> unknown , unknown>
     */
    public function getProjectSendStatus() {
        $pID = $_REQUEST["pID"];
//                        $pID = 1392;
//                        $sSite = 'zf';
        $sSite = $_REQUEST["site"];
        $project = D("Project");
        $aStatus = $project->where("projectID=" . $pID . " AND site='" . $sSite . "'")->field("sendStatus,cid,pid")->find();
//                        var_dump($aStatus);
//                        die();
        if (empty($aStatus)) {
            $aList = 0;
        } else {
            if ($aStatus["sendStatus"] == 0) {
                $aList = $project->query("SELECT projectID,webPage,name,site FROM `project` WHERE level >= 4 AND numbers > 0 AND cid=" . $aStatus["cid"] . " AND status AND sendStatus=1 AND site in ('28','ls') ORDER BY `site` DESC limit 8");
                if (count($aList == 0))
//					$aList = $project->query("SELECT projectID,webPage,name,site FROM `project` WHERE level >= 4 AND numbers > 0 AND pid=".$aStatus["pid"]." AND sendStatus=1 AND site in ('28','ls') ORDER BY `site` DESC limit 8");
                    $aList[0]["status"] = 0;
            }else {
                $aList[0]["status"] = 1;
            }
        }
        echo json_encode($aList);
    }

    /**
     * 获取某个项目的推荐项目列表
     * @param unknown $iPID
     * @return string
     */
    public function getRecommendList($iPID, $sSite) {
        $project = D("Project");
        $pInfo = $project->where("projectID=" . $aInfo["projectID"] . " AND site='" . $sSite . "'")->find();
        $aSendProjectList = $project->where(" subCat='" . $pInfo["subCat"] . "' AND sendStatus=1 AND status =1 AND site='28'")->order(" rand()")->limit(10)->select();
        if (count($aSendProjectList) == 0) { //该小分类没有信息
            $aSendProjectList = $project->where(" catName='" . $pInfo["catName"] . "' AND sendStatus=1 AND status =1  AND site='28'")->order(" rand()")->limit(10)->select();
        }
        for ($i = 0; $i < count($aSendProjectList); $i++) {
            $aSendProjectList[$i]["http"] = "http://tj.28.com" . $aSendProjectList[$i]["webPage"];
        }
        return $aSendProjectList;
    }

    /**
     * 每天设置发送状态为1，表示可以接受,每天执行一次，而且只能执行一次
     */
    public function setSendStatusByDay() {
        $p = M("Project");
        $iStatus = $p->where("status > 0")->setField("sendStatus", 1);
        var_dump($iStatus);
        return $iStatus;
    }

    /**
     * 定时执行，20分钟一次，查询是否有项目不能发送，如果不能发送的话，设置字段为0
     * @return Ambigous <boolean, unknown, false, number>
     */
    public function setSendStatusByProject() {
        $p = M("Project");
        $gb = A("Guestbook");
        $today = date("Y-m-d");
        $aList = $gb->getDenyProjectList();
        for ($i = 0; $i < count($aList); $i++) {
            $p->where(" site= '" . $aList[$i]["site"] . "' AND projectID=" . $aList[$i]["project_id"])->setField("sendStatus", 0);
        }
        $this->display("index");
    }

    /**
     * 分类管理
     */
    public function category() {
        $act = ($_REQUEST["act"] == "") ? "list" : $_REQUEST["act"];
        switch ($act) {
            case "add":
            if (!empty($_POST))
                $this->_addCategory($_POST);
            else {
                $aBigList = $this->_listCategory(1);
                $this->assign("list", $aBigList);
                $this->display("category_add");
            }
            break;
            case "list":
            $alist = $this->_listCategory();
            $this->assign("list", $alist);
            $this->display("category_list");
            break;
            case "edit":
            $this->_editcategory();
            break;
            case "update":
            $this->_updatecategory($_REQUEST);
            break;
            case "del":
            $this->_delcatagory();
            break;
        }
    }

    /**
     * 递归分类列表
     */
    public function resortCategory($data, $pid = 0, $level = 0) {
        var_dump($data);
        Static $data = array();
        foreach ($data as $k => $v) {
            if ($v[‘pid’] == $pid) {
                $data[] = $v;
                resort($data, $v[‘id’] , $level + 1);
            }
        }
        return $data;
    }

    /**
     * 添加分类
     */
    private function _addCategory($aD) {
        $result = D("Category")->add($aD);
        if ($result) {
            $this->success('新增成功', U('/Admin/Project/Category'));
        } else {
            $this->error('新增失败');
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
     * 编辑分类
     */
    private function _editcategory() {
        $id = $_REQUEST["id"];
        $one = D("category")->where("id=$id")->select();
        //var_dump($one);
        $alist = D("category")->where("pid=0")->select();
        //var_dump($alist);
        $this->assign("one", $one);
        $this->assign("alist", $alist);
        $this->display("category_edit");
    }

    Private function _updatecategory($aD) {
        //var_dump($aD);
        $id = $aD["id"];
        $pid = $aD["pid"];
        $catname = $aD["catname"];
        $data['pid'] = $pid;
        $data['catname'] = $catname;
        $result = D("Category")->where("id=$id")->save($data);
        if ($result) {
            $this->success('修改成功', U('/Admin/Project/Category'));
        } else {
            $this->error('修改失败');
        }
    }

    /**
     * 删除分类
     */
    private function _delcatagory() {
        $id = $_REQUEST["id"];
        $result = D("Category")->where("id=$id")->delete();
        if ($result) {
            $this->success('删除成功', U('Admin/Project/Category'));
        } else {
            $this->error('删除失败', U('Admin/Project/Category'));
        }
    }

    /**
     * 同步项目信息
     * @param unknown $sSite
     */
    public function syncProject() {
        $p = M("project");
        $data = array('status'=>'0','sendStatus'=>'1');
        $rs = $p->where(1)->setField('status', '0');
        if ($rs) {
            echo 'update status is 0 succeed<br />';
            echo 'update sendStatus is 1 succeed';
        }
//                    die();
        if ($this->_syncFrom28())
            echo "Sync 28 is ok<br/>";
        if ($this->_syncFromZF())
            echo "Sync zf is ok<br/>";
        if ($this->_syncFromLS())
            echo "Sync ls is ok<br/>";
        if ($this->_syncFromWP())
            echo "Sync wp is ok<br/>";
        if ($this->_syncFrom91())
            echo "Sync 91 is ok<br/>";
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
//					if($aT["statusStr"] == "空闲" || $aT["statusStr"] == "停用")
//						$aTemp["status"] = 0;
//					else
//						$aTemp["status"] = 1;
                //$aTemp["status"] = $aT["statusStr"] == "空闲" ? 0:1;
                if ($aT["isControl"] == '0') {
                    $aTemp["status"] = 0;
                } else {
                    if ($aT["statusStr"] == "空闲" || $aT["statusStr"] == "停用") {
                        $aTemp["status"] = 0;
                    } else {
                        $aTemp["status"] = 1;
                    }
                }
                $aTemp["name"] = $aT["name"];
                $aTemp["webPage"] = $aT["adWebPage"];
                $aTemp["backCall"] = $aT["mobnum"];
                $aTemp["needNum"] = ($aT["gbookNum"] + 1);
                $aTemp["numbers"] = ($aT["gbookNum"] + 1);
                $aTemp["level"] = 5;
                $aTemp["site"] = "28";
                $aTemp["catName"] = $aT["topCateName"];
                $aTemp["subCat"] = $aT["subCateName"];
                $aTemp["custid"] = $aT["custid"]; //custid 为企业ID号
                $aTemp["seat"] = $aT["seat"]; //seat 为项目方坐席标识ID，注19位数字
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
     * 同步WP的信息
     * Time:2014/07/30 15:31
     * By siyuan
     */
    private function _syncFromWP() {
        $p = M("Project");
        $aSource = xml2array(C("xml_wp"));
        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
            $aT = $aSource["log"]["fields"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["projectID"] = $aT["projectID"];
                $aTemp["status"] = 1;
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["adWebPage"];
                $aTemp["needNum"] = $aT["row"];
                $aTemp["numbers"] = $aT["row"];
                $aTemp["level"] = 5;
                $aTemp["site"] = "wp";
                $aTemp["catName"] = $aT["catName"];
                $aTemp["subCat"] = $aT["subCat"];
                //如果数据已经存在，则更新数据，否则就是插入数据
//                                        var_dump($aTemp);
//                                        die();
                $id = $p->where("projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
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
//                        $p-> where("site = zf")->setField('status','0');
        $aSource = xml2array(C("xml_zf"));
        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
            $aT = $aSource["log"]["fields"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["clientID"] = 0;
                $aTemp["projectID"] = $aT["projectID"];
                if ($aT["num"] > 0) {
                    $aTemp["status"] = 1;
                } else {
                    $aTemp["status"] = 0;
                }
//                                        if($aT["gbookNumMax"] > 0){
//                                            $aTemp["status"]  = 1;
//                                        }else{
//                                            $aTemp["status"]  = 0;
//                                        }
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["web"];
//					$aTemp["backCall"] = $aT["tel"];
                $aTemp["needNum"] = $aT["num"];
                $aTemp["numbers"] = $aT["num"];
                $aTemp["level"] = 5;
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
         * 测试同步连锁网的信息
         */
        public function syncFromLS() {
            $p = M("Project");
            //                        $p-> where("site = ls")->setField('status','0');
            $aSource = xml2array(C("xml_ls"));
            $j = 0;
            for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
                $aT = $aSource["log"]["fields"][$i];
                if ($aT["projectID"] > 0) {
                    $aTemp = array();
                    $aTemp["clientID"] = 0;
                    $aTemp["projectID"] = $aT["projectID"];
                    $aTemp["status"] = 1;
                            //不需要dsp的项目，作为暂时调整
                            // if ($aTemp["projectID"] == 135557 || $aTemp["projectID"] == 135626 || $aTemp["projectID"] == 135861) {
                            //     $aTemp["status"] = 0;
                            // }
                    $aTemp["name"] = $aT["projectName"];
                    $aTemp["webPage"] = $aT["adWebPage"];
                    $aTemp["backCall"] = $aT["link"];
                    if ($aTemp["backCall"] == null) {
                        $aTemp["backCall"] = 'NO TEL';
                    }
                    if (!isset($aT["endtime"])){
                        $aTemp["level"] = 5;
                            // $aTemp["needNum"] = 5;
                            // $aTemp["numbers"] = 5;
                    }else{
                     $aTemp["level"]  = 4;
                 }
                 $aTemp["needNum"] = $aT["dspnum"];
                 $aTemp["numbers"] = $aT["dspnum"];
                 $aTemp["site"] = ls;
                   // $aTemp["catName"] = $aT["industry"];
                   // $aTemp["subCat"] = $aT["subindustry"];
                            //如果数据已经存在，则更新数据，否则就是插入数据
                 $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
                 if ($id > 0){
                    // $j++;
                    // var_dump($aTemp);
                    $rs = $p->where("id=" . $id)->save($aTemp);
                    // var_dump($rs);
                }   else{
                    // $j++;
                    // var_dump($aTemp);
                    $d = $p->add($aTemp);
                    echo $p->getLastsql().'<br />';
                    // var_dump($d);
                }
                unset($aTemp);
            }
            // echo $j.'<br />';
        }
    }

    /**
     * 同步连锁网的信息
     */
    private function _syncFromLS() {
        $p = M("Project");
//                        $p-> where("site = ls")->setField('status','0');
        $aSource = xml2array(C("xml_ls"));
        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
            $aT = $aSource["log"]["fields"][$i];
            if ($aT["projectID"] > 0) {
                $aTemp = array();
                $aTemp["clientID"] = 0;
                $aTemp["projectID"] = $aT["projectID"];
                $aTemp["status"] = 1;
                //不需要dsp的项目，作为暂时调整
                // if ($aTemp["projectID"] == 135557 || $aTemp["projectID"] == 135626 || $aTemp["projectID"] == 135861) {
                //     $aTemp["status"] = 0;
                // }
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["adWebPage"];
                $aTemp["backCall"] = $aT["link"];
                if ($aTemp["backCall"] == null) {
                    $aTemp["backCall"] = 'NO TEL';
                }
                if (!isset($aT["endtime"])){
                    $aTemp["level"] = 5;
                // $aTemp["needNum"] = 5;
                // $aTemp["numbers"] = 5;
                }else{
                 $aTemp["level"]  = 4;
                   // $aTemp["needNum"] = 1;
                   // $aTemp["numbers"] = 1;
             }
             $aTemp["needNum"] = $aT["dspnum"];
             $aTemp["numbers"] = $aT["dspnum"];
             $aTemp["site"] = ls;
               // $aTemp["catName"] = $aT["industry"];
               // $aTemp["subCat"] = $aT["subindustry"];
                //如果数据已经存在，则更新数据，否则就是插入数据
             $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
             if ($id > 0)
                $p->where("id=" . $id)->save($aTemp);
            else
                $d = $p->data($aTemp)->add();
            unset($aTemp);
        }
    }
//                        $this->display(index);
    return true;
}
//    private function _syncFromLS() {
//        $p = M("Project");
////                        $p-> where("site = ls")->setField('status','0');
//        $aSource = xml2array(C("xml_ls"));
//        for ($i = 0; $i < count($aSource["log"]["fields"]); $i++) {
//            $aT = $aSource["log"]["fields"][$i];
//            if ($aT["projectID"] > 0) {
//                $aTemp = array();
//                $aTemp["clientID"] = 0;
//                $aTemp["projectID"] = $aT["projectID"];
//                $aTemp["status"] = 1;
//                //不需要dsp的项目，作为暂时调整
//                if ($aTemp["projectID"] == 135557 || $aTemp["projectID"] == 135626 || $aTemp["projectID"] == 135861) {
//                    $aTemp["status"] = 0;
//                }
//                $aTemp["name"] = $aT["projectName"];
//                $aTemp["webPage"] = $aT["adWebPage"];
//                $aTemp["backCall"] = $aT["link"];
//                if ($aTemp["backCall"] == null) {
//                    $aTemp["backCall"] = 'NO TEL';
//                }
//                switch ($aT["level"]) {
//                    case "银牌会员":
//                    case "VIP会员":
//                        $level = 3;
//                        break;
//                    case "按效果付费":
//                        $level = 5;
//                        break;
//                    case "广告客户":
//                        $level = 4;
//                        break;
//                    default:
//                        $level = 2;
//                        break;
//                }
//                $aTemp["level"] = $level;
//                $aTemp["needNum"] = 3;
//                $aTemp["numbers"] = 3;
//                if ($aTemp["level"] == 4) {
//                    $aTemp["needNum"] = 1;
//                    $aTemp["numbers"] = 1;
//                }
//                if ($aTemp["name"] == '快汇宝') {
//                    $aTemp["needNum"] = 100;
//                    $aTemp["numbers"] = 100;
//                }
//                $aTemp["site"] = ls;
//                $aTemp["catName"] = $aT["industry"];
//                $aTemp["subCat"] = $aT["subindustry"];
//                //如果数据已经存在，则更新数据，否则就是插入数据
//                $id = $p->where("clientID=" . $aTemp["clientID"] . " AND projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
////                                        var_dump($id);
//                if ($id > 0)
//                    $p->where("id=" . $id)->save($aTemp);
//                else
////						$d = $p->add($aTemp);
//                    $d = $p->data($aTemp)->add();
////                                        var_dump($d);
//                unset($aTemp);
//            }
//        }
////                        $this->display(index);
//        return true;
//    }

    /**
     * 同步91加盟网的信息
     */
    private function _syncFrom91() {
        $p = M("project");
        $aSource = file_get_contents(C("json_91"));
//        var_dump($aSource);
//        die();
        $aSource = json_decode($aSource, true);
//        echo '<pre>';
//        var_dump($aSource);
//        die();
        header("content-type:text/html;charset=utf-8");
        $aSource = $aSource["data"];
        $aTemp = array();
        foreach ($aSource as $a => $val) {
//            var_dump($val);
            $aTemp["projectID"] = $val['pid'];
            $aTemp["name"] = $val['project_name'];
            $aTemp["site"] = "91";
            $aTemp["status"] = 1;
            $aTemp["level"] = 4;
            $aTemp["needNum"] = 1;
            $aTemp["numbers"] = 1;
            //煲上皇和速汇宝
            if($aTemp["projectID"] == '307' || $aTemp["projectID"] == '308'){
                $aTemp["needNum"] = 100;
                $aTemp["numbers"] = 100;
            }
            //乾通国际
            elseif(in_array($aTemp["projectID"],array(137))){
                $aTemp["needNum"] = 4;
                $aTemp["numbers"] = 4;
            }
            #限制数量5条
            #小粥仙(合户)=>269,sooe品位生活灯饰=>296,放鹅郎=>277 ,热狗铺子=>315 ,乐姿家电（搜易）=>218 ,乐姿家电2（搜易）=>300 ,考拉大冒险(搜易)=>287
            elseif(in_array($aTemp["projectID"],array(269,296,277,315,218,300,287))){
                $aTemp["needNum"] = 5;
                $aTemp["numbers"] = 5;
            }
            #限制数量3条
            #sooe爱儿乐=>313,sooe世纪学习吧=>280
            elseif (in_array($aTemp["projectID"],array(313,280))) {
                $aTemp["needNum"] = 3;
                $aTemp["numbers"] = 3;
            }
            #限制数量2条
            #考拉大冒险(合户)=>289
            elseif (in_array($aTemp["projectID"],array(289))) {
                $aTemp["needNum"] = 2;
                $aTemp["numbers"] = 2;
            }
//                        如果数据已经存在，则更新数据，否则就是插入数据
//            var_dump($aTemp);
            $id = $p->where("projectID=" . $aTemp["projectID"] . " AND site='" . $aTemp["site"] . "'")->getField("id");
            if ($id > 0) {
//                echo '修改';
                $p->where("id=" . $id)->save($aTemp);
            } else {
//                echo '增加';
                $p->add($aTemp);
            }
        }
        unset($aTemp);
        return true;
    }
}
