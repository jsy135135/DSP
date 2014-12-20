<?php

/**
 * 数据处理类
 * @author Dinglei
 *
 */
class DataStatisticsAction extends OQAction {
    /*
     * 单独查询28转接数量
     * Time:2014-12-1 11:06:33
     * By:siyuan
     */
    public function transfer_list(){
        $dealed = M("DataDealed");
        $transfer_date = date("Y-m-d", strtotime("31 days ago"));
        $aList_transfer = $dealed->query("select addDate,count(distinct phone) as count from data_dealed where transfer=1 AND addDate >= '" . $transfer_date . "'group by addDate");
//        var_dump($aList_transfer);
        $this->assign("aList_transfer", $aList_transfer);
        $this->display();
    }
    #已经排重

    public function index() {
        $dealed = M("DataDealed");
        $dealAgain = D("DataAgain");
        $guestbook = D("guestbook");
        // var_dump($_REQUEST);
        $period = $_REQUEST["period"] == "" ? 7 : $_REQUEST["period"];
        $startdate = $_REQUEST["startdate"];
        $enddate = $_REQUEST["enddate"];
        $aList = array();
        $aTotalList = $this->getNumBySite("all", "total", $period,$startdate,$enddate); //全部的成功数
        $aList_28 = $this->getNumBySite("28", "total", $period,$startdate,$enddate); //28的总成功数
        $aList_zf = $this->getNumBySite("zf", "total", $period,$startdate,$enddate); //致富的总成功数
        $aList_ls = $this->getNumBySite("ls", "total", $period,$startdate,$enddate); //连锁的总成功数
        $aList_wp = $this->getNumBySite("wp", "total", $period,$startdate,$enddate); //wp的总成功数
        $aList_91 = $this->getNumBySite("91", "total", $period,$startdate,$enddate); //91的总成功数
        $aSend = $this->getNumBySite("all", ">0", $period,$startdate,$enddate); //全部发送成功数
        $aSend_28 = $this->getNumBySite("28", ">0", $period,$startdate,$enddate); //28发送成功数
        $aSend_zf = $this->getNumBySite("zf", ">0", $period,$startdate,$enddate); //zf发送成功数
        $aSend_ls = $this->getNumBySite("ls", ">0", $period,$startdate,$enddate); //ls发送成功数
        $aSend_wp = $this->getNumBySite("wp", ">0", $period,$startdate,$enddate); //wp的发送成功数
        $aSend_91 = $this->getNumBySite("91", ">0", $period,$startdate,$enddate); //91的发送成功数
        $aFail = $this->getNumBySite("all", "<0", $period,$startdate,$enddate); //全部发送成功数
        $aFail_28 = $this->getNumBySite("28", "<0", $period,$startdate,$enddate); //28发送失败数
        $aFail_zf = $this->getNumBySite("zf", "<0", $period,$startdate,$enddate); //zf发送失败数
        $aFail_ls = $this->getNumBySite("ls", "<0", $period,$startdate,$enddate); //ls发送失败数
        $aFail_wp = $this->getNumBySite("wp", "<0", $period,$startdate,$enddate); //wp的发送失败数
        $aFail_91 = $this->getNumBySite("91", "<0", $period,$startdate,$enddate); //91的发送失败数

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
//                echo '<pre>';
               // var_dump($aList);
        $this->assign("aList", $aList);
        $this->assign("startdate",$startdate);
        $this->assign("enddate",$enddate);
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
//                echo '<pre>';
//                var_dump($aList);
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
        // $dToday = "2014-05-01";
        $iCount = D("DataDealed")->where("again = 0 AND status <0 AND u_id=" . $uID . " AND addDate = '" . $dDate . "'")->count();
        /* if($iCount == 0)
          {
          $aData["u_id"] = $uID;
          D("DataDealed")->where("again = 0 AND status <0 AND u_id=0 AND addDate >= '".$dDate."'")->order(" id asc ")->limit(100)->save($aData);
          } */
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

    private function getNumBySite($sSite = '28', $iType = 'total', $period = "7",$startdate = "",$enddate = "") {
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
//                        $dDate = "2014-05-01";
            $dDate_end = date("Y-m", strtotime("last month")) . "-31";
//                        $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "agotwo") {
//			$dDate = date("Y-m", strtotime("last month"))."-01";
            $dDate = "2014-05-01";
//			$dDate_end = date("Y-m", strtotime("last month"))."-31";
            $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "showforday") {
            $dDate = $startdate;
            $dDate_end = $enddate;
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        }
        // echo $sSQL;
        // die();

        $dealed = M("data_dealed");
        $aList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
//         echo '<pre>';
//         echo $siyuan = "SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL GROUP BY addDate";
//        var_dump($aList);
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
//                        $dDate = "2014-05-01";
                $dDate_end = date("Y-m", strtotime("last month")) . "-31";
//                        $dDate_end = "2014-05-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            } elseif ($period == "agotwo") {
//			$dDate = date("Y-m", strtotime("last month"))."-01";
                $dDate = "2014-05-01";
//			$dDate_end = date("Y-m", strtotime("last month"))."-31";
                $dDate_end = "2014-05-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            }
            $QaList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
//            echo $siyuan = "SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL GROUP BY addDate";
//         echo '<br />';
//        var_dump($QaList);
            $i = 0;
            $j = 0;
            foreach ($QaList as &$day) {
                $day[t] = 0;
                foreach ($aList as $Day) {
                    if ($day[addDate] == $Day[addDate]) {
//                        echo 1;
                        $day[t] = $Day[t];
                    }
                    $i++;
                }
                $j++;
            }
//            echo $i.'<br />';
//            echo $j.'<br />';
//        var_dump($QaList);
//            $aList = array_merge($QaList, $aList);
//            echo '<pre>';
//            var_dump($QaList);
            //判断如果是status<0的话，那就返回重组之后的数组
            if ($iType == "<0") {
                return $QaList;
            }
        }
        return $aList;
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
//                        $dDate = "2014-05-01";
            $dDate_end = date("Y-m", strtotime("last month")) . "-31";
//                        $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        } elseif ($period == "agotwo") {
//			$dDate = date("Y-m", strtotime("last month"))."-01";
            $dDate = "2014-05-01";
//			$dDate_end = date("Y-m", strtotime("last month"))."-31";
            $dDate_end = "2014-05-31";
            $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
        }

        $dealed = M("data_dealed");
        $aList = $dealed->query("SELECT COUNT(phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
//         echo '<pre>';
//         echo $siyuan = "SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL GROUP BY addDate";
//        var_dump($aList);
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
//                        $dDate = "2014-05-01";
                $dDate_end = date("Y-m", strtotime("last month")) . "-31";
//                        $dDate_end = "2014-05-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            } elseif ($period == "agotwo") {
//			$dDate = date("Y-m", strtotime("last month"))."-01";
                $dDate = "2014-05-01";
//			$dDate_end = date("Y-m", strtotime("last month"))."-31";
                $dDate_end = "2014-05-31";
                $sSQL .= " AND addDate between '" . $dDate . "' AND '" . $dDate_end . "' AND u_id!=0";
            }
            $QaList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
//            echo $siyuan = "SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL GROUP BY addDate";
//         echo '<br />';
//        var_dump($QaList);
            $i = 0;
            $j = 0;
            foreach ($QaList as &$day) {
                $day[t] = 0;
                foreach ($aList as $Day) {
                    if ($day[addDate] == $Day[addDate]) {
//                        echo 1;
                        $day[t] = $Day[t];
                    }
                    $i++;
                }
                $j++;
            }
//            echo $i.'<br />';
//            echo $j.'<br />';
//        var_dump($QaList);
//            $aList = array_merge($QaList, $aList);
//            echo '<pre>';
//            var_dump($QaList);
            //判断如果是status<0的话，那就返回重组之后的数组
            if ($iType == "<0") {
                return $QaList;
            }
        }
        return $aList;
    }

    /**
     *
     * @param string $sSite
     * @param string $iType
     * @param string $period
     */
//	private function getNumBySite($sSite='28', $iType='total',$period="7"){
//		$sSQL = "1";
//		$sSQL .= ($sSite =="all") ? "" : " AND site='".$sSite."'";
//		$sSQL .= ($iType == "total") ? "" : " AND status $iType";
//		if(is_numeric($period)) {
//			$dDate = date("Y-m-d", strtotime("$period days ago"));
//			$sSQL .= " AND  addDate >= '".$dDate."' AND u_id!=0 AND u_id!=0";
//		} elseif ($period == "month") {
//			$dDate = date("Y-m", strtotime("this month"))."-01";
//			$dDate_end = date("Y-m", strtotime("this month"))."-31";
//			$sSQL .= " AND addDate between '".$dDate."' AND '".$dDate_end."' AND u_id!=0";
//		}elseif ($period == "prev") {
//			$dDate = date("Y-m", strtotime("last month"))."-01";
////                        $dDate = "2014-05-01";
//			$dDate_end = date("Y-m", strtotime("last month"))."-31";
////                        $dDate_end = "2014-05-31";
//			$sSQL .= " AND addDate between '".$dDate."' AND '".$dDate_end."' AND u_id!=0";
//		}elseif ($period == "agotwo") {
////			$dDate = date("Y-m", strtotime("last month"))."-01";
//                        $dDate = "2014-05-01";
////			$dDate_end = date("Y-m", strtotime("last month"))."-31";
//                        $dDate_end = "2014-05-31";
//			$sSQL .= " AND addDate between '".$dDate."' AND '".$dDate_end."' AND u_id!=0";
//		}
//
//		$dealed = M("data_dealed");
//		$aList = $dealed->query("SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL AND u_id <> 10086 AND regular=1 GROUP BY addDate");
//		// echo '<pre>';
//		// echo $siyuan = "SELECT COUNT(DISTINCT phone) AS t ,addDate FROM `data_dealed` WHERE $sSQL GROUP BY addDate";
//		// var_dump($aList);
//		return $aList;
//	}

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

}
