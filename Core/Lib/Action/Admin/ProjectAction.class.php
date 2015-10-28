<?php

/*
 * 项目管理信息
 */

class ProjectAction extends Action {

    public function index() {
        //显示当前在使用的项目列表
        $act = ($_REQUEST["act"] == "") ? "1" : $_REQUEST["act"];
        $p = M("Project");
        $Data = M("data_dealed");
        $Sdata = M("data_again");
        $date = date("Y-m-d");
        if ($act == 1) {
            $aList = $p->where("status = 1 AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND level  >=4 order by site asc,catName asc,subCat asc");
        } else {
            $aList = $p->where("status = 1 AND site = '" . $act . "' AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND site = '" . $act . "' AND level  >=4 order by site asc,catName asc,subCat asc");
        }
        foreach ($aList as &$r) {
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
            if ($r["transfer"] == 1) {
                $r["transfer"] = '是';
            } else
                $r["transfer"] = '否';
        }
        $countaList = count($aList);
        for ($i = 0; $i < $countaList; $i++) {
            $aList["$i"]["alcount"] = ($aList["$i"]["needNum"] - $aList["$i"]["numbers"]); //留言已经提交数
            $aList["$i"]["talcount"] = ($aList["$i"]["tneed"] - $aList["$i"]["tnum"]); //转接已经提交数
        }
        $aCatList = D("category")->where(1)->getField("id,catname");
        $this->assign("countaList", $countaList);
        $this->assign("catList", $aCatList);
        $this->assign("aList", $aList);
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
        $this->assign("BigList", $BigList);
        $this->assign("SmallList", $SmallList);
        $this->display();
    }

    public function indexforo() {
        //显示当前在使用的项目列表
        $act = ($_REQUEST["act"] == "") ? "1" : $_REQUEST["act"];
        $p = M("Project");
        $Data = M("data_dealed");
        $Sdata = M("data_again");
        $g = M("guestbook");
        $date = date("Y-m-d", strtotime("1day ago"));
        if ($act == 1) {
            $aList = $p->where("status = 1 AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND level  >=4 order by site asc,catName asc,subCat asc");
        } else {
            $aList = $p->where("status = 1 AND site = '" . $act . "' AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
            $Asum = $p->query("select sum(needNum) as Asum,sum(numbers) as Anumbers from project where status = 1 AND site = '" . $act . "' AND level  >=4 order by site asc,catName asc,subCat asc");
        }
        foreach ($aList as &$r) {
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
            if ($r["transfer"] == 1) {
                $r["transfer"] = '是';
            } else
                $r["transfer"] = '否';
        }
        $countaList = count($aList);
        for ($i = 0; $i < $countaList; $i++) {
            $aList[$i]['rtt'] = $g->where("project_id = '" . $aList[$i]['projectID'] . "' AND site = '" . $aList[$i]['site'] . "'AND add_date = '" . $date . "'")->count();
            $aList["$i"]["alcount"] = ($aList["$i"]["needNum"] - $aList["$i"]["numbers"]); //留言已经提交数
            $aList["$i"]["talcount"] = ($aList["$i"]["tneed"] - $aList["$i"]["tnum"]); //转接已经提交数
        }
        $aCatList = D("category")->where(1)->getField("id,catname");
        $this->assign("countaList", $countaList);
        $this->assign("catList", $aCatList);
        $this->assign("aList", $aList);
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
        $this->assign("BigList", $BigList);
        $this->assign("SmallList", $SmallList);
        $this->display();
    }

    /*
     * Time:2014-11-14 14:55:18
     * 定时同步之后，更新每个项目已经发送de数量
     * By:siyuan
     */

    public function dealtotel() {
        $p = M("project");
        $D = M("data_dealed");
        $d = M("data_again");
        $date = date("Y-m-d");
        $prolist = $p->where("status = 1 AND level  >=4 ")->order('site asc,catName asc,subCat asc')->select();
        $prolistcount = count($prolist);
        for ($i = 0; $i < $prolistcount; $i++) {
            $site = $prolist[$i]['site'];
            $projectID = $prolist[$i]['projectID'];
            $fristCount = $D->where("addDate = '" . $date . "'AND site = '" . $site . "' AND projectID = '" . $projectID . "' AND status >= 0 AND status <> 8 AND ((`check` = 0 AND `regular` = 0) or (`check` = 1 AND `regular` = 1)) ")->count(); //此项目今日一次发送成功数
            $scendCount = $d->where("addDate = '" . $date . "'AND site = '" . $site . "' AND project_id = '" . $projectID . "' AND status >= 0 AND status <> 8 AND ((`check` = 0 AND `regular` = 0) or (`check` = 1 AND `regular` = 1)")->count(); //此项目今天二次发送成功数
            $numbers = ($prolist["$i"]["needNum"] - $fristCount - $scendCount); //已经发送的数据量
            $rs = $p->where("projectid = '" . $projectID . "' AND site = '" . $site . "'")->setField("numbers", $numbers);
        }
        return true;
    }

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

    /*
     * ajax修改分类
     * time:2014/09/09 12:03
     * By siyuan
     *
     */

    public function update() {
        $aD = $_REQUEST;
        $id = $aD["listid"];
        $pid = $aD["pid"];
        $cid = $aD["cid"];
        $result = D("Project")->execute("update project set pid=" . $pid . ", cid=" . $cid . " where id=" . $id);
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
        $sSite = $_REQUEST["site"];
        $project = D("Project");
        $aStatus = $project->where("projectID=" . $pID . " AND site='" . $sSite . "'AND status > 0 ")->field("sendStatus,cid,pid")->find();
        if (empty($aStatus)) {
            $aList = 0;
        } else {
            if ($aStatus["sendStatus"] == 0) {
                $aList = $project->query("SELECT projectID,webPage,name,site FROM `project` WHERE level >= 4 AND numbers > 0 AND cid=" . $aStatus["cid"] . " AND status AND sendStatus=1 AND site in ('28','ls') ORDER BY `site` DESC limit 8");
                if (count($aList == 0)) {
                    $aList[0]["status"] = 0;
                }
            } else {
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
            if ($v['pid'] == $pid) {
                $data[] = $v;
                resort($data, $v['id'], $level + 1);
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
        $alist = D("category")->where("pid=0")->select();
        $this->assign("one", $one);
        $this->assign("alist", $alist);
        $this->display("category_edit");
    }

    Private function _updatecategory($aD) {
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
        $data = array('status' => '0', 'sendStatus' => '1');
        $rs = $p->where(1)->setField($data);
        if ($rs) {
            echo 'update status is 0 succeed<br />';
            echo 'update sendStatus is 1 succeed';
        }
        if ($this->_syncFrom28())
            echo "Sync 28 is ok<br/>";
        if ($this->_syncFromZF())
            echo "Sync zf is ok<br/>";
        if ($this->_syncFromLS())
            echo "Sync ls is ok<br/>";
        if ($this->_syncFromWP())
            echo "Sync wp is ok<br/>";
        if ($this->_GetProjectListBy91())
            echo "Sync 91 is ok<br/>";
        if ($this->dealtotel())
            echo "Update totol";
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
                $aTemp["needNum"] = 3;
                $aTemp["numbers"] = 3;
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
                $huibo_flag = $aT["huibo_flag"];
                if ($aT["huibo_flag"] == 'no') {
                    $huibo_flag = 0;
                } else {
                    $huibo_flag = 1;
                }
                $aTemp["transfer"] = $huibo_flag;
                $aTemp["name"] = $aT["projectName"];
                $aTemp["webPage"] = $aT["web"];
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
                if ($aTemp["backCall"] == null) {
                    $aTemp["backCall"] = 'NO TEL';
                }
                if (!isset($aT["endtime"])) {
                    $aTemp["level"] = 5;
                    // $aTemp["needNum"] = 5;
                    // $aTemp["numbers"] = 5;
                } else {
                    $aTemp["level"] = 4;
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

    /**
     * 同步91加盟网的信息
     */
    private function _syncFrom91() {
        $p = M("project");
        $aSource = file_get_contents(C("json_91"));
        $aSource = json_decode($aSource, true);
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
            $aTemp["needNum"] = 20;
            $aTemp["numbers"] = 20;
            #DSP手动暂停项目
            if (in_array($aTemp["projectID"], array(64, 82, 97, 120, 123, 151, 192, 296, 134, 135, 137, 170, 258, 269, 267, 270, 277, 278, 287, 300, 311, 315, 308, 280, 247, 249, 304, 309, 316, 305, 389, 289, 456, 136, 313, 290, 460, 487, 985, 465, 462, 463, 464, 467, 687, 991, 965, 975, 967, 491, 459, 461, 974, 689, 600, 921, 992, 436, 178, 987, 954, 975, 991, 989, 674, 921, 556, 990, 599, 935, 541, 579, 973, 959))) {
                $aTemp["status"] = 0;
            }
            if (strpos($aTemp['name'], '连锁2')) {
                $aTemp["status"] = 0;
            }
            //煲上皇和速汇宝
            if ($aTemp["projectID"] == '307' || $aTemp["projectID"] == '308' || $aTemp["projectID"] == '491' || $aTemp["projectID"] == '487' || $aTemp["projectID"] == '581' || $aTemp["projectID"] == '197') {
                $aTemp["needNum"] = 100;
                $aTemp["numbers"] = 100;
            }
            if (in_array($aTemp["projectID"], array(270, 483, 482, 465, 687))) {
                $aTemp["needNum"] = 15;
                $aTemp["numbers"] = 15;
            } elseif (in_array($aTemp["projectID"], array(978))) {
                $aTemp["needNum"] = 13;
                $aTemp["numbers"] = 13;
            }
            //乾通国际
            elseif (in_array($aTemp["projectID"], array(134, 135, 136, 137, 190))) {
                $aTemp["needNum"] = 4;
                $aTemp["numbers"] = 4;
            } elseif (in_array($aTemp["projectID"], array(965, 999))) {
                $aTemp["needNum"] = 10;
                $aTemp["numbers"] = 10;
            }
            #限制数量5条
            #小粥仙(合户)=>269,sooe品位生活灯饰=>296,放鹅郎=>277 ,热狗铺子=>315 ,乐姿家电（搜易）=>218 ,乐姿家电2（搜易）=>300 ,考拉大冒险(搜易)=>287
            elseif (in_array($aTemp["projectID"], array(267, 269, 296, 277, 305, 218, 300, 287, 389, 290, 459, 460, 461, 462, 463, 464, 467))) {
                $aTemp["needNum"] = 5;
                $aTemp["numbers"] = 5;
            }
            #限制数量3条
            #sooe爱儿乐=>313,sooe世纪学习吧=>280
            elseif (in_array($aTemp["projectID"], array(313, 280, 436, 437, 985, 987, 65))) {
                $aTemp["needNum"] = 3;
                $aTemp["numbers"] = 3;
            }
            #限制数量2条
            #考拉大冒险(合户)=>289
            elseif (in_array($aTemp["projectID"], array(289))) {
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

    /*
     * 更新插入91的网址页面地址
     * Time:2015年7月28日16:37:42
     * By:siyuan
     */

    public function updateurl_91() {
        $url_array = array(
            '黄冈网校（广告位）' => 'http://www.91jmw.com/data/hgzxwx/index.html',
            '小时代童装（91）' => 'http://www.91jmw.com/data/xsdtz/index.html',
            '十二生肖童装（91）' => 'http://www.91jmw.com/data/sesxtz/index.html',
            '金芒果童装（91）' => 'http://www.91jmw.com/data/jmgtz/index.html',
            '考拉大冒险（专题）' => 'http://www.91jmw.com/data/kldmx2/index.html',
            '佐纳利男装（91）' => 'http://www.91jmw.com/data/znlnz/index.html',
            '搜富国际（广告位）' => 'http://www.91jmw.com/data/sfgj/index.html',
            '巴宝顿（91）' => 'http://www.91jmw.com/data/bbdnz/index.html',
            '炙口福(91)' => 'http://www.91jmw.com/data/zkfu/index.html',
            '怡品饰家(91)' => 'http://www.91jmw.com/data/ypsjjj/index.html',
            '泰工照明（91）' => 'http://www.91jmw.com/data/tgzmds/index.html',
            '星期六儿童烘培乐园（91）' => 'http://www.91jmw.com/data/xqlhp/index.html',
            '川香百味鸡（专题）' => 'http://www.91jmw.com/data/chx/index.html',
        );
        $project = M("project");
        foreach ($url_array as $key => $value) {
//            echo $value.'<br >';
            $data['webPage'] = $value;
//            echo $data['webPage'].'<br />';
            $rs = $project->where("site = '91' AND name like '%" . $key . "%'")->save($data);
            echo $rs . '<br />';
        }
    }

    //分析91竞价投放来源关键词的标识
    public function KeyWordFor91() {
        $guestbook = M("guesbook");
        $data = $guestbook->query("SELECT address,count(*) as t  FROM `guestbook` WHERE `add_date` = '2015-08-24' AND `site` LIKE '91' group by address order by t desc");
//        var_dump($data);
        $data_count = count($data);
        for ($i = 0; $i < $data_count; $i++) {
            $urldata = parse_url($data[$i]['address']);
            $data[$i]['keywordstag'] = $urldata['query'];
        }
        var_dump($data);
    }

    //分析竞价投放来源关键词的标识By详细项目ID
    public function KeyWordFor91ByProjectId() {
        $guestbook = M("guestbook");
        $date = date("Y-m-d");
        $data = $guestbook->query("SELECT address,count(*) as t  FROM `guestbook` WHERE `add_date` = '2015-08-24' AND `site` LIKE '91' AND project_id = '987'  group by address order by t desc");
        var_dump($data);
    }

    /*
     * 同步91加盟网的项目列表信息
     * Time:
     * By:siyuan
     */

    private function _GetProjectListBy91() {
        $p = M("project");
        $aSource = file_get_contents(C("json_91_new"));
        $aSource = json_decode($aSource, true);
        header("content-type:text/html;charset=utf-8");
        $aSource = $aSource["data"];
        $aTemp = array();
        foreach ($aSource as $a => $val) {
            $aTemp["projectID"] = $val['pid'];
            $aTemp["name"] = $val['project_name'];
            $aTemp["site"] = "91";
            $aTemp["status"] = $val['dsp_status'];
            $aTemp["level"] = 4;
            $aTemp["needNum"] = $val['dsp_num'];
            $aTemp["numbers"] = $val['dsp_num'];
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
