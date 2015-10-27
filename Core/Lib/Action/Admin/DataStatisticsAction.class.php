<?php

/**
 * 数据处理类
 * @author Dinglei
 *
 */
class DataStatisticsAction extends OQAction {

    /**
     * 源量来源分析
     * Time:2015-3-30 14:44:41
     * By:siyuan
     */
    public function OriginalNum() {
        $g = M("guestbook");
        $startday = $_REQUEST['startdate'] == "" ? date("Y-m-d", strtotime("7 days ago")) : $_REQUEST['startdate'];
        $endday = $_REQUEST['enddate'] == "" ? date("Y-m-d") : $_REQUEST['enddate'];
        $totol = $g->query("select add_date,count(*) as t from guestbook where add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量
        $Ytotol = $g->query("select add_date,count(*) as t from guestbook where project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量
        $Ntotol = $g->query("select add_date,count(*) as t from guestbook where project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量
        $totol_28 = $g->query("select add_date,count(*) as t from guestbook where site = '28' AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量 28
        $Ytotol_28 = $g->query("select add_date,count(*) as t from guestbook where site = '28' AND project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量 28
        $Ntotol_28 = $g->query("select add_date,count(*) as t from guestbook where site = '28' AND project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量 28
        $totol_ls = $g->query("select add_date,count(*) as t from guestbook where site = 'ls' AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量 ls
        $Ytotol_ls = $g->query("select add_date,count(*) as t from guestbook where site = 'ls' AND project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量 ls
        $Ntotol_ls = $g->query("select add_date,count(*) as t from guestbook where site = 'ls' AND project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量 ls
        $totol_zf = $g->query("select add_date,count(*) as t from guestbook where site = 'zf' AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量 zf
        $Ytotol_zf = $g->query("select add_date,count(*) as t from guestbook where site = 'zf' AND project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量 zf
        $Ntotol_zf = $g->query("select add_date,count(*) as t from guestbook where site = 'zf' AND project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量 zf
        $totol_wp = $g->query("select add_date,count(*) as t from guestbook where site = 'wp' AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量 wp
        $Ytotol_wp = $g->query("select add_date,count(*) as t from guestbook where site = 'wp' AND project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量 wp
        $Ntotol_wp = $g->query("select add_date,count(*) as t from guestbook where site = 'wp' AND project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量 wp
        $totol_91 = $g->query("select add_date,count(*) as t from guestbook where site = '91' AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量 91
        $Ytotol_91 = $g->query("select add_date,count(*) as t from guestbook where site = '91' AND project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量 28
        $Ntotol_91 = $g->query("select add_date,count(*) as t from guestbook where site = '91' AND project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量 
        $totolcount = count($totol);
        for ($i = 0; $i < $totolcount; $i++) {
            $tdate = $totol[$i]['add_date'];
            $totol[$i]['totol'] = $totol[$i]['t'];
            $totol[$i]['Ytotol'] = $Ytotol[$i]['t'];
            $totol[$i]['Ntotol'] = $Ntotol[$i]['t'];
            $totol[$i]['totol_28'] = $totol_28[$i]['t'];
            $totol[$i]['Ytotol_28'] = $Ytotol_28[$i]['t'];
            $totol[$i]['Ntotol_28'] = $Ntotol_28[$i]['t'];
            $totol[$i]['totol_ls'] = $totol_ls[$i]['t'];
            $totol[$i]['Ytotol_ls'] = $Ytotol_ls[$i]['t'];
            $totol[$i]['Ntotol_ls'] = $Ntotol_ls[$i]['t'];
            $totol[$i]['totol_zf'] = $totol_zf[$i]['t'];
            $totol[$i]['Ytotol_zf'] = $Ytotol_zf[$i]['t'];
            $totol[$i]['Ntotol_zf'] = $Ntotol_zf[$i]['t'];
            $totol[$i]['totol_wp'] = $totol_wp[$i]['t'];
            $totol[$i]['Ytotol_wp'] = $Ytotol_wp[$i]['t'];
            $totol[$i]['Ntotol_wp'] = $Ntotol_wp[$i]['t'];
            $totol[$i]['totol_91'] = $totol_91[$i]['t'];
            $totol[$i]['Ytotol_91'] = $Ytotol_91[$i]['t'];
            $totol[$i]['Ntotol_91'] = $Ntotol_91[$i]['t'];
        }
        $this->assign("totol", $totol);
        $this->display();
    }

    /*
     * 单独查询28转接数量
     * Time:2014-12-1 11:06:33
     * By:siyuan
     */

    public function transfer_list() {
        $dealed = M("DataDealed");
        $transfer_date = date("Y-m-d", strtotime("31 days ago"));
        $aList_transfer = $dealed->query("select addDate,count(distinct phone) as count from data_dealed where transfer=1 AND addDate >= '" . $transfer_date . "'group by addDate");
        $this->assign("aList_transfer", $aList_transfer);
        $this->display();
    }

    #已经排重

    public function index() {
        $dealed = M("DataDealed");
        $dealAgain = D("DataAgain");
        $guestbook = D("guestbook");
        $period = $_REQUEST["period"] == "" ? 7 : $_REQUEST["period"];
        $startdate = $_REQUEST["startdate"];
        $enddate = $_REQUEST["enddate"];
        $aList = array();
        $aTotalList = $this->getNumBySite("all", "total", $period, $startdate, $enddate); //全部的成功数
        $aList_28 = $this->getNumBySite("28", "total", $period, $startdate, $enddate); //28的总成功数
        $aList_zf = $this->getNumBySite("zf", "total", $period, $startdate, $enddate); //致富的总成功数
        $aList_ls = $this->getNumBySite("ls", "total", $period, $startdate, $enddate); //连锁的总成功数
        $aList_wp = $this->getNumBySite("wp", "total", $period, $startdate, $enddate); //wp的总成功数
        $aList_91 = $this->getNumBySite("91", "total", $period, $startdate, $enddate); //91的总成功数
        $aSend = $this->getNumBySite("all", ">0", $period, $startdate, $enddate); //全部发送成功数
        $aSend_28 = $this->getNumBySite("28", ">0", $period, $startdate, $enddate); //28发送成功数
        $aSend_zf = $this->getNumBySite("zf", ">0", $period, $startdate, $enddate); //zf发送成功数
        $aSend_ls = $this->getNumBySite("ls", ">0", $period, $startdate, $enddate); //ls发送成功数
        $aSend_wp = $this->getNumBySite("wp", ">0", $period, $startdate, $enddate); //wp的发送成功数
        $aSend_91 = $this->getNumBySite("91", ">0", $period, $startdate, $enddate); //91的发送成功数
        $aFail = $this->getNumBySite("all", "<0", $period, $startdate, $enddate); //全部发送成功数
        $aFail_28 = $this->getNumBySite("28", "<0", $period, $startdate, $enddate); //28发送失败数
        $aFail_zf = $this->getNumBySite("zf", "<0", $period, $startdate, $enddate); //zf发送失败数
        $aFail_ls = $this->getNumBySite("ls", "<0", $period, $startdate, $enddate); //ls发送失败数
        $aFail_wp = $this->getNumBySite("wp", "<0", $period, $startdate, $enddate); //wp的发送失败数
        $aFail_91 = $this->getNumBySite("91", "<0", $period, $startdate, $enddate); //91的发送失败数

        for ($i = 0; $i < count($aTotalList); $i++) {
            $Ydate = $aTotalList[$i]["addDate"];
            $aList[$i]["OriginalNum"] = $guestbook->where("add_date = '" . $Ydate . "' AND project_id > 0")->count(); //首次外呼对一的每日数据源量
            $aList[$i]["OriginalNumO"] = $guestbook->where("add_date = '" . $Ydate . "' AND project_id = 0")->count(); //首次外呼对多的每日数据源量
            $aList[$i]["OriginalNum_All"] = $guestbook->where("add_date = '" . $Ydate . "'")->count(); //每日源数据总量
            $aList[$i]["addDate"] = $aTotalList[$i]["addDate"];
            $aList[$i]["total_all"] = $aTotalList[$i]["t"];
            $aList[$i]["totol_liuyan"] = $dealed->where("addDate = '" . $Ydate . "'AND transfer <> 1 AND `check` = '1' AND regular = 1")->count("DISTINCT phone");
            $aList[$i]["totol_transfer"] = $dealed->where("addDate = '" . $Ydate . "'AND transfer = 1 AND `check` = '1' AND regular = 1")->count("DISTINCT phone");
            $aList[$i]["totol_ratio"] = round($aList[$i]["totol_transfer"] / $aList[$i]["total_all"], 3) * 100;
            $aList[$i]["total_28"] = $aList_28[$i]["t"];
            $aList[$i]["total_zf"] = $aList_zf[$i]["t"];
            $aList[$i]["total_wp"] = $aList_wp[$i]["t"];
            $aList[$i]["total_91"] = $aList_91[$i]["t"];
            $aList[$i]["send_all"] = $aSend[$i]["t"];
            $aList[$i]["send_28"] = $aSend_28[$i]["t"];
            $aList[$i]["send_zf"] = $aSend_zf[$i]["t"];
            $aList[$i]["send_wp"] = $aSend_wp[$i]["t"];
            $aList[$i]["send_91"] = $aSend_91[$i]["t"];
            $aList[$i]["fail_all"] = $aFail[$i]["t"];
            $aList[$i]["fail_28"] = $aFail_28[$i]["t"];
            $aList[$i]["fail_zf"] = $aFail_zf[$i]["t"];
            $aList[$i]["fail_wp"] = $aFail_wp[$i]["t"];
            $aList[$i]["fail_91"] = $aFail_91[$i]["t"];
            $aList[$i]["total_ls"] = $aList_ls[$i]["t"];
            $aList[$i]["send_ls"] = $aSend_ls[$i]["t"];
            $aList[$i]["ls_transfer"] = $dealed->where("addDate = '" . $Ydate . "'AND transfer = 1 AND site = 'ls' AND regular = 1")->count("DISTINCT phone");
            $aList[$i]["ls_liuyan"] = $dealed->where("addDate = '" . $Ydate . "'AND transfer <> 1 AND site = 'ls' AND regular = 1")->count("DISTINCT phone");
            $aList[$i]["fail_ls"] = $aFail_ls[$i]["t"];
            $aList[$i]["ratio"] = round($aList[$i]["send_all"] / $aList[$i]["total_all"], 3) * 100;
            $aList[$i]["28ratio"] = round($aList[$i]["fail_28"] / $aList[$i]["total_28"], 3) * 100;
            $aList[$i]["zfratio"] = round($aList[$i]["fail_zf"] / $aList[$i]["total_zf"], 3) * 100;
            $aList[$i]["lsratio"] = round($aList[$i]["fail_ls"] / $aList[$i]["total_ls"], 3) * 100;
            $aList[$i]["wpratio"] = round($aList[$i]["fail_wp"] / $aList[$i]["total_wp"], 3) * 100;
            $aList[$i]["91ratio"] = round($aList[$i]["fail_91"] / $aList[$i]["total_91"], 3) * 100;
            $aList[$i]["send_again"] = $dealAgain->where("add_date='" . $aList[$i]["addDate"] . "' AND status >0")->count();
        }
        $this->assign("aList", $aList);
        $this->assign("startdate", $startdate);
        $this->assign("enddate", $enddate);
        $this->display();
    }

    #没有排重

    public function Dindex() {
        $dealed = M("DataDealed");
        $dealAgain = D("DataAgain");
        $guestbook = D("guestbook");
        $period = $_REQUEST["period"] == "" ? 7 : $_REQUEST["period"];
        $aList = array();
        $aTotalList = $this->DgetNumBySite("all", "total", $period); //全部的成功数
        $aList_28 = $this->DgetNumBySite("28", "total", $period); //28的总成功数
        $aList_zf = $this->DgetNumBySite("zf", "total", $period); //致富的总成功数
        $aList_ls = $this->DgetNumBySite("ls", "total", $period); //连锁的总成功数
        $aList_wp = $this->DgetNumBySite("wp", "total", $period); //wp的总成功数
        $aList_91 = $this->DgetNumBySite("91", "total", $period); //91的总成功数
        $aSend = $this->DgetNumBySite("all", ">0", $period); //全部发送成功数
        $aSend_28 = $this->DgetNumBySite("28", ">0", $period); //28发送成功数
        $aSend_zf = $this->DgetNumBySite("zf", ">0", $period); //zf发送成功数
        $aSend_ls = $this->DgetNumBySite("ls", ">0", $period); //ls发送成功数
        $aSend_wp = $this->DgetNumBySite("wp", ">0", $period); //wp的发送成功数
        $aSend_91 = $this->DgetNumBySite("91", ">0", $period); //91的发送成功数
        $aFail = $this->DgetNumBySite("all", "<0", $period); //全部发送成功数
        $aFail_28 = $this->DgetNumBySite("28", "<0", $period); //28发送失败数
        $aFail_zf = $this->DgetNumBySite("zf", "<0", $period); //zf发送失败数
        $aFail_ls = $this->DgetNumBySite("ls", "<0", $period); //ls发送失败数
        $aFail_wp = $this->DgetNumBySite("wp", "<0", $period); //wp的发送失败数
        $aFail_91 = $this->DgetNumBySite("91", "<0", $period); //91的发送失败数

        for ($i = 0; $i < count($aTotalList); $i++) {
            $Ydate = $aTotalList[$i]["addDate"];
            $aList[$i]["OriginalNum"] = $guestbook->where("add_date = '" . $Ydate . "' AND project_id > 0")->count(); //首次外呼对一的每日数据源量
            $aList[$i]["OriginalNumO"] = $guestbook->where("add_date = '" . $Ydate . "' AND project_id = 0")->count(); //首次外呼对多的每日数据源量
            $aList[$i]["addDate"] = $aTotalList[$i]["addDate"];
            $aList[$i]["total_all"] = $aTotalList[$i]["t"];
            $aList[$i]["total_28"] = $aList_28[$i]["t"];
            $aList[$i]["total_zf"] = $aList_zf[$i]["t"];
            $aList[$i]["total_wp"] = $aList_wp[$i]["t"];
            $aList[$i]["total_91"] = $aList_91[$i]["t"];
            $aList[$i]["send_all"] = $aSend[$i]["t"];
            $aList[$i]["send_28"] = $aSend_28[$i]["t"];
            $aList[$i]["send_zf"] = $aSend_zf[$i]["t"];
            $aList[$i]["send_wp"] = $aSend_wp[$i]["t"];
            $aList[$i]["send_91"] = $aSend_91[$i]["t"];
            $aList[$i]["fail_all"] = $aFail[$i]["t"];
            $aList[$i]["fail_28"] = $aFail_28[$i]["t"];
            $aList[$i]["fail_zf"] = $aFail_zf[$i]["t"];
            $aList[$i]["fail_wp"] = $aFail_wp[$i]["t"];
            $aList[$i]["fail_91"] = $aFail_91[$i]["t"];
            $aList[$i]["total_ls"] = $aList_ls[$i]["t"];
            $aList[$i]["send_ls"] = $aSend_ls[$i]["t"];
            $aList[$i]["fail_ls"] = $aFail_ls[$i]["t"];
            $aList[$i]["ratio"] = round($aList[$i]["send_all"] / $aList[$i]["total_all"], 3) * 100;
            $aList[$i]["28ratio"] = round($aList[$i]["fail_28"] / $aList[$i]["total_28"], 3) * 100;
            $aList[$i]["zfratio"] = round($aList[$i]["fail_zf"] / $aList[$i]["total_zf"], 3) * 100;
            $aList[$i]["lsratio"] = round($aList[$i]["fail_ls"] / $aList[$i]["total_ls"], 3) * 100;
            $aList[$i]["wpratio"] = round($aList[$i]["fail_wp"] / $aList[$i]["total_wp"], 3) * 100;
            $aList[$i]["91ratio"] = round($aList[$i]["fail_91"] / $aList[$i]["total_91"], 3) * 100;
            $aList[$i]["send_again"] = $dealAgain->where("add_date='" . $aList[$i]["addDate"] . "' AND status >0")->count();
        }
        $this->assign("aList", $aList);
        $this->display();
    }

    /**
     * 数据二次处理
     */
    public function again() {
        $uID = session("username");
        if ($uID == "admin")
            $uID = 826; //默认外呼标记为826

        $dDate = $_REQUEST["date"] == "" ? date("Y-m-d", strtotime("1 days ago")) : $_REQUEST["date"];
        $iCount = D("DataDealed")->where("again = 0 AND status <0 AND u_id=" . $uID . " AND addDate = '" . $dDate . "'")->count();
        $aList = D("DataDealed")->where("again = 0 AND status <0 AND u_id=$uID AND addDate = '" . $dDate . "'")->limit(100)->select();
        $this->assign("iCount", $iCount);
        $this->assign("uID", $uID);
        $this->assign("dDate", $dDate);
        $this->assign("aList", $aList);
        $this->display();
    }

    /**
     * 设置留言为已经二次处理，但不审核，这种情况基本表示此留言已经废弃
     * @return Ambigous <boolean, unknown>
     */
    public function abandon() {
        $id = $_REQUEST["id"];
        $aData["again"] = 1;
        return D("DataDealed")->where("id=" . $id)->save($aData);
    }

    #已经排重

    private function getNumBySite($sSite = '28', $iType = 'total', $period = "7", $startdate = "", $enddate = "") {
        $sSQL = "1";
        $sSQL .= ($sSite == "all") ? "" : " AND site='" . $sSite . "'";
        $sSQL .= ($iType == "total") ? "" : " AND status $iType";
        if (is_numeric($period)) {
            $dDate = date("Y-m-d", strtotime("$period days ago"));
            $sSQL .= " AND  addDate >= '" . $dDate . "' AND u_id!=0 AND u_id!=10086";
            $dDate_end = date("Y-m-d");
        } elseif ($period == "month") {
            $dDate = date("Y-m", strtotime("this month")) . "-01";
            $dDate_end = date("Y-m", strtotime("this month")) . "-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "prev") {
            $dDate = date("Y-m", strtotime("last month")) . "-01";
            $dDate_end = date("Y-m", strtotime("last month")) . "-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "agotwo") {
            $dDate = "2014-05-01";
            $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "showforday") {
            $dDate = $startdate;
            $dDate_end = $enddate;
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        }
        $dealed = M("data_dealed");
        $guestbook = M("guestbook");
        $aList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
        $aList_date = $guestbook->query("SELECT add_date FROM `guestbook` WHERE add_date>='" . $dDate . "' AND add_date<='" . $dDate_end . "'group by add_date");
        foreach ($aList_date as &$value) {
            $value['t'] = '0';
            $value['addDate'] = $value['add_date'];
            foreach ($aList as &$avalue) {
                if ($avalue['addDate'] == $value['add_date']) {
                    $value['t'] = $avalue['t'];
                }
            }
        }
        return $aList_date;
    }

    #没有进行排重复的

    private function DgetNumBySite($sSite = '28', $iType = 'total', $period = "7") {
        $sSQL = "1";
        $sSQL .= ($sSite == "all") ? "" : " AND site='" . $sSite . "'";
        $sSQL .= ($iType == "total") ? "" : " AND status $iType";
        if (is_numeric($period)) {
            $dDate = date("Y-m-d", strtotime("$period days ago"));
            $sSQL .= " AND  addDate >= '" . $dDate . "' AND u_id!=0 AND u_id!=10086";
        } elseif ($period == "month") {
            $dDate = date("Y-m", strtotime("this month")) . "-01";
            $dDate_end = date("Y-m", strtotime("this month")) . "-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "prev") {
            $dDate = date("Y-m", strtotime("last month")) . "-01";
            $dDate_end = date("Y-m", strtotime("last month")) . "-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "agotwo") {
            $dDate = "2014-05-01";
            $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        }
        $dealed = M("data_dealed");
        $aList = $dealed->query("SELECT COUNT(phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
        if ($iType == "<0") {
            $iType1 = ">0";
            $sSQL = "1";
            $sSQL .= ($sSite == "all") ? "" : " AND site='" . $sSite . "'";
            $sSQL .= ($iType1 == "total") ? "" : " AND status $iType1";
            if (is_numeric($period)) {
                $dDate = date("Y-m-d", strtotime("$period days ago"));
                $sSQL .= " AND  addDate >= '" . $dDate . "' AND u_id!=0 AND u_id!=10086";
            } elseif ($period == "month") {
                $dDate = date("Y-m", strtotime("this month")) . "-01";
                $dDate_end = date("Y-m", strtotime("this month")) . "-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            } elseif ($period == "prev") {
                $dDate = date("Y-m", strtotime("last month")) . "-01";
                $dDate_end = date("Y-m", strtotime("last month")) . "-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            } elseif ($period == "agotwo") {
                $dDate = "2014-05-01";
                $dDate_end = "2014-05-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            }
            $QaList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
            $i = 0;
            $j = 0;
            foreach ($QaList as &$day) {
                $day[t] = 0;
                foreach ($aList as $Day) {
                    if ($day[addDate] == $Day[addDate]) {
                        $day[t] = $Day[t];
                    }
                    $i++;
                }
                $j++;
            }
            if ($iType == "<0") {
                return $QaList;
            }
        }
        return $aList;
    }
    /**
     * 分析数据并入库
     */
    public function analysis() {
        $dDate = $_REQUEST["date"];
        if (empty($dDate))
            $dDate = date("Y-m-d", strtotime("1 days ago"));  //昨天
        $this->_analysisByDay($dDate);
        $this->display();
    }

    /**
     * 根据条件获取数量
     * @param  $iProjectID 项目ID
     * @param Date $dDate	时间
     * @param varchar $sSite	网站
     * @param string $iType	类型
     * @return integer
     */
    public function getNumByProject($iProjectID, $dDate, $sSite, $iType = "callOk") {
        $dealed = M("DataDealed");
        $sSQL = "addDate='" . $dDate . "' AND site='" . $sSite . "' AND projectID=$iProjectID";
        if ($iType == "sendOk")
            $sSQL .= " AND status >0";
        else
            $sSQL .="";
        $iCount = $dealed->where($sSQL)->count();
        return $iCount;
    }

    /**
     * 根据条件获得所有的信息列表
     * @param unknown $iProjectID
     * @param unknown $dDate
     * @param unknown $sSite
     * @param string $iType
     * @return Ambigous <mixed, boolean, NULL, string, unknown, multitype:, multitype:multitype: , void, object>
     */
    public function getListByProject($iProjectID, $dDate, $sSite, $iType = "callOk") {
        $dealed = M("DataDealed");
        $sSQL = "addDate='" . $dDate . "' AND site='" . $sSite . "' AND projectID=$iProjectID";
        if ($iType == "sendOk")
            $sSQL .= " AND status >0";
        elseif ($iType == "deny")
            $sSQL .= " AND status < 0";
        else
            $sSQL .="";
        $aList = $dealed->where($sSQL)->select();
        return $aList;
    }

    /**
     * 分析每日数据
     * @param unknown $dDate
     */
    private function _analysisByDay($dDate) {
        $dealed = M("DataDealed");
        $dealed->where("addDate='" . $dDate . "'")->delete(); //先删除这个日子的所有数据
        if (empty($dDate))
            $dDate = date("Y-m-d");
        $sFileName = "./Log/hf-" . $dDate . ".txt";
        echo $sFileName;
        $fp = file($sFileName);
        $j = 0;
        for ($i = 0; $i < count($fp); $i++) {
            echo $i . "<br>";
            $r = explode("#", $fp[$i]);
            $a = unserialize($r[3]);
            $site = $r[1];
            if (in_array($site, C("ls")))
                $siteName = "ls";
            elseif (in_array($site, C("zf")))
                $siteName = "zf";
            else
                $siteName = "28";
            $aR = array();
            $aR["name"] = $a["user_name"];
            $aR["phone"] = $a["phone"];
            $aR["address"] = $a["address"];
            $aR["domain"] = $r[1];
            $aR["projectID"] = $a["project_id"];
            $aR["site"] = $siteName;
            $aR["ip"] = $a["ips"];
            $aR["status"] = $r[2];
            $aR["addDate"] = $r[0];

            $dealed->add($aR);
        }
    }

    /**
     * 针对每日源数据量，所对应的项目，进行数据分析和工作详细划分部署
     * Time：2015年8月11日14:52:20
     * By:siyuan
     */
    public function getCountForProjectByDate() {
        $g = M("guestbook");
        $p = M("project");
        $date = date("Y-m-d",strtotime("1 days ago"));
        $data = $g->query("SELECT project_id as pid,count(*) as t,site as gsite from guestbook where add_date = '".$date."' group by project_id order by gsite asc,t desc");
        $data_count = count($data);
        for($i=0;$i<$data_count;$i++){
           $project_data = $p->field("name,needNum,numbers,site as psite,status,sendstatus")->where("site = '".$data[$i]['gsite']."' AND projectID = '".$data[$i]['pid']."'")->select();
           $data[$i]['name'] = $project_data[0]['name'];
           $data[$i]['psite'] = $project_data[0]['psite'];
           $data[$i]['needNum'] = $project_data[0]['needNum'];
           $data[$i]['numbers'] = $project_data[0]['numbers'];
           $data[$i]['status'] = $project_data[0]['status'];
           $data[$i]['sendstatus'] = $project_data[0]['sendstatus'];
           if($data[$i]['name'] == ''){
               unset($data[$i]);
           }
           if($data[$i]['status'] == '0'){
               unset($data[$i]);
           }
           if($data[$i]['psite'] == '28'){
               unset($data[$i]);
           }
        }
        $listCount = count($data);
        $this->assign("date" , $date);
        $this->assign("listCount", $listCount);
        $this->assign("data", $data);
        $this->display();
    }
    
     //分析91竞价投放来源关键词的标识
    public function KeyWordFor91() {
        $projectID = $_REQUEST['projectID'];
        $site = $_REQUEST['site'];
        $guestbook = M("guesbook");
        $date = date("Y-m-d",strtotime("1 days ago"));
        $data = $guestbook->query("SELECT address,count(*) as t  FROM `guestbook` WHERE `add_date` = '".$date."' AND `site` LIKE '".$site."' AND project_id = '".$projectID."' group by address order by t desc");
        $data_count = count($data);
        for ($i = 0; $i < $data_count; $i++) {
            $urldata = parse_url($data[$i]['address']);
            $data[$i]['keywordstag'] = $urldata['query'];
        }
        $this->assign('data',$data);
        $this->display();
    }
}
