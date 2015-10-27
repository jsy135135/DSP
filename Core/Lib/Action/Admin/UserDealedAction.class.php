<?php

/*
 * 话务信息的处理结果
 * 
 */
class UserDealedAction extends OQAction {

    //每人分配数据详细
    public function show() {
        $startdate = $_REQUEST["startDate"] == "" ? date("Y-m-d") : $_REQUEST["startDate"]; //开始日期
        $enddate = $_REQUEST["endDate"] == "" ? date("Y-m-d") : $_REQUEST["endDate"]; //结束日期
        $guestbook = M("guestbook");
        $dealed = D("data_dealed");
        $user = D("user");
        $sql = "SELECT u_id,COUNT(DISTINCT phone) AS tp_count FROM `guestbook` WHERE ( Thedate >= '".$startdate."' AND Thedate <= '".$enddate."') group by u_id";
            $data = $guestbook->query($sql);
            foreach ($data as $key => &$value) {
                $value['name'] = $user->where("username = '".$value['u_id']."'")->getField("remark");
            }
            $this->assign('data',$data);
            $this->assign('startDate',$startdate);
            $this->assign('endDate',$enddate);
            $this->display();
    }

    /*
     * 已经排重
     * 
     */
    public function index($date = 'date("Y-m-d")') {
        $startdate = $_REQUEST["startdate"]; //开始日期
        $enddate = $_REQUEST["enddate"]; //结束日期
        $date = date("Y-m-d");
        if ($_REQUEST["startdate"] || $REQUEST["enddate"]) {
            #各个表中相同的属性字段名不一样，所以这里定义三条
            $sSQL = "addDate >= '" . $startdate . "' AND addDate <= '" . $enddate . "'";
            $sSQL1 = "deal_date >= '" . $startdate . "' AND deal_date <= '" . $enddate . "'";
            $sSQL2 = "add_date >= '" . $startdate . "' AND add_date <= '" . $enddate . "'";
            $sSQL3 = "Thedate >= '" . $startdate . "' AND Thedate <= '" . $enddate . "'";
        } else {
            $sSQL = "addDate = '" . $date . "'";
            $sSQL1 = "deal_date = '" . $date . "'";
            $sSQL2 = "add_date = '" . $date . "'";
            $sSQL3 = "Thedate = '" . $date . "'";
        }
        $dealed = D("data_dealed");
        $lswx = D("lswx");
        $zfwx = D("zfwx");
        $again = D("data_again");
        $guestbook = D("guestbook");
        $user = D("user");
        //循环出多个用户名，然后依次进行数据的查询
        //选择当天的参与的UID
        $aUserList = $dealed->where($sSQL . "AND u_id <> 0 AND u_id <>10086")->field(" DISTINCT u_id as uid")->order('u_id asc')->select();
        //当天参与UID对应的客服姓名(remark)
        $aUserListcount = count($aUserList) - 1;
        for ($j = 0; $j <= $aUserListcount; $j++) {
            $i = $aUserList[$j]["uid"];
            $aList[$i]["uid"] = $i;
            $aList[$i]["username"] = $user->where("username=" . $i)->getField("remark");
            $aList[$i]["section"] = $user->where("username=" . $i)->getField("section");
            $aList[$i]["aim"] = $user->where("username=" . $i)->getField("aim");
            $aList[$i]["yunyin"] = $user->where("username=" . $i)->getField("");
            $aList[$i]["firstCallNum"] = $guestbook->where($sSQL1 . "AND u_id= '" . $i . "'")->count("DISTINCT phone"); //首次外呼处理数
            $aList[$i]["firstSendNum"] = $dealed->where($sSQL . "AND u_id=$i")->count("DISTINCT phone"); //首次提交数
            $aList[$i]["transfer"] = $dealed->where($sSQL . "AND u_id=$i AND transfer = '1' AND site = '28' ")->count("DISTINCT phone"); //28转接数量
            $aList[$i]["transferOKNum"] = $dealed->where($sSQL . "AND u_id=$i AND transfer = '1' AND site = '28' AND regular = '1' ")->count("DISTINCT phone"); //28转接审核有效数
            $aList[$i]["firstSendOKNum"] = $dealed->where($sSQL . "AND u_id=$i AND status>0")->count("DISTINCT phone"); //首次提交成功数
            $aList[$i]["firstSendOKRatio"] = round($aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"], 4) * 100; //首次提交成功比
            $aList[$i]["againCallNum"] = $again->where($sSQL2 . "AND u_id=$i")->count("DISTINCT data_id");
            $aList[$i]["againSubmitNum"] = $again->where($sSQL2 . "AND u_id=$i")->count("DISTINCT  data_id");
            $aList[$i]["againSubmitOkNum"] = $again->where($sSQL2 . "AND u_id=$i AND status > 0")->count("DISTINCT data_id");
            $aList[$i]["lsSunbmitNum"] = $dealed->where($sSQL . "AND u_id=$i AND status>0 AND site = 'ls'")->count("DISTINCT phone");
            $aList[$i]["lstransfer"] = $dealed->where($sSQL . "AND u_id=$i AND (content like '%电话咨询%' or content like '%电话联系过了%' or transfer = '1') AND status>0 AND site = 'ls'")->count("DISTINCT phone");
            $aList[$i]["zfSunbmitNum"] = $dealed->where($sSQL . "AND u_id=$i AND status>0 AND site = 'zf'")->count("DISTINCT phone");
            $aList[$i]["zftransfer"] = $dealed->where($sSQL . "AND u_id=$i AND status>0 AND site = 'zf' AND transfer = '1'")->count("DISTINCT phone");
            $aList[$i]["lswx"] = $lswx->where($sSQL . "AND u_id=$i")->count();
            $aList[$i]["lszjwx"] = $lswx->where($sSQL . "AND u_id = $i AND (content like '%电话咨询%' or content like '%电话联系过了%' or content like '%转接%')")->count();
            $aList[$i]["zfwx"] = $zfwx->where($sSQL . "AND u_id=$i AND zfwx <> 'ok'")->count();
            $aList[$i]["zfzjwx"] = $zfwx->where($sSQL . "AND u_id = $i AND zfwx <> 'ok' AND content like '%转接%'")->count();
            $aList[$i]["lsSunbmitOK"] = round(($aList[$i]["lswx"] / $aList[$i]["lsSunbmitNum"]), 4) * 100;
            $aList[$i]["zfSunbmitOK"] = round(($aList[$i]["zfwx"] / $aList[$i]["zfSunbmitNum"]), 4) * 100;
        }
        $aList = array_values($aList);
        $aListjson = json_encode($aList);
        $aListcount = count($aList);
        $this->assign('aListcount', $aListcount);
        $this->assign('username', $username);
        $this->assign('date', $date);
        $this->assign('aListjson', $aListjson);
        $this->assign('startdate', $startdate);
        $this->assign('enddate', $enddate);
        $this->display();
    }

    #没有排重

    public function Dindex($date = 'date("Y-m-d")') {
        $date = $_REQUEST["date"] == "" ? date("Y-m-d") : $_REQUEST["date"];
        $dealed = D("data_dealed");
        $lswx = D("lswx");
        $again = D("data_again");
        $guestbook = D("guestbook");
        $user = D("user");
        //循环出多个用户名，然后依次进行数据的查询
        //选择当天的参与的UID
        $aUserList = $dealed->where("addDate =  '" . $date . "'AND u_id <> 0 AND u_id <>10086")->field(" DISTINCT u_id as uid")->order('u_id asc')->select();
        //当天参与UID对应的客服姓名(remark)
        $aUserListcount = count($aUserList) - 1;
        for ($j = 0; $j <= $aUserListcount; $j++) {
            $i = $aUserList[$j]["uid"];
            $aList[$i]["uid"] = $i;
            $aList[$i]["username"] = $user->where("username=" . $i)->getField("remark");
            $aList[$i]["aim"] = $user->where("username=" . $i)->getField("aim");
            $aList[$i]["yunyin"] = $user->where("username=" . $i)->getField("");
            $aList[$i]["firstCallNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id= '" . $i . "'")->count("phone"); //首次外呼处理数
            $aList[$i]["firstSendNum"] = $dealed->where("addDate =  '" . $date . "' AND u_id=$i")->count("phone"); //首次提交数
            $aList[$i]["firstSendOKNum"] = $dealed->where("addDate =  '" . $date . "' AND u_id=$i AND status>0")->count("phone"); //首次提交成功数
            $aList[$i]["firstSendOKRatio"] = round($aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"], 4) * 100; //首次提交成功比
            $aList[$i]["againCallNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i")->count("data_id");
            $aList[$i]["againSubmitNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i")->count(" data_id");
            $aList[$i]["againSubmitOkNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i AND status > 0")->count("data_id");
            $aList[$i]["lsSunbmitNum"] = $dealed->where("addDate =  '" . $date . "' AND u_id=$i AND status>0 AND site = 'ls'")->count("phone");
            $aList[$i]["lswx"] = $lswx->where("addDate =  '" . $date . "' AND u_id=$i")->count();
            if ($aList[$i]["lsws"] == 0) {
                $aList[$i]["lswx"] == "没有数据";
            }
            $aList[$i]["lsSunbmitOK"] = round(($aList[$i]["lswx"] / $aList[$i]["lsSunbmitNum"]), 4) * 100;
        }
        $aListcount = count($aList);
        $this->assign('aListcount', $aListcount);
        $this->assign('username', $username);
        $this->assign('date', $date);
        $this->assign('aList', $aList);
        $this->display();
    }

    public function index_bak($date = 'date("Y-m-d")') {
        $date = $_REQUEST["date"] == "" ? date("Y-m-d") : $_REQUEST["date"];
        $dealed = D("data_dealed");
        $again = D("data_again");
        $guestbook = D("guestbook");
        //循环出多个用户名，然后依次进行数据的查询
        //选择当天的参与的UID
        $aUserList = $guestbook->where("deal_date =  '" . $date . "' ")->field(" DISTINCT u_id as uid")->select();
        for ($j = 0; $j <= count($aUserList); $j++) {
            $i = $aUserList[$j]["uid"];
            $aList[$i]["uid"] = $i;
            $aList[$i]["firstCallNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i")->count(); //首次外呼处理数
            $aList[$i]["firstSendNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i AND send_status <> ''")->count(); //首次提交数
            $aList[$i]["firstSendOKNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i AND send_status>0")->count(); //首次提交成功数
            $aList[$i]["firstSendOKRatio"] = round($aList[$i]["firstSendOKNum"] / $aList[$i]["firstSendNum"], 4) * 100; //首次提交成功比
            $aList[$i]["secondCallNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i AND again=1 ")->count(); //二次外呼处理数
            $aList[$i]["secondSendNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i AND send_status <> ''  AND again=1")->count(); //二次提交数
            $aList[$i]["secondSendOKNum"] = $guestbook->where("deal_date =  '" . $date . "' AND u_id=$i AND send_status>0  AND again=1")->count(); //二次提交成功数
            $aList[$i]["secondSendOKRatio"] = round($aList[$i]["secondSendOKNum"] / $aList[$i]["secondSendNum"], 4) * 100; //二次提交成功比
            $aList[$i]["againCallNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i")->count(); //二次外呼处理数
            $aList[$i]["againSubmitNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i")->count(); //二次提交数
            $aList[$i]["againSubmitOkNum"] = $again->where("add_date =  '" . $date . "' AND u_id=$i AND status > 0")->count(); //二次提交成功数
            $aList[$i]["specialOkNum"] = $dealed->where("addDate =  '" . $date . "' AND u_id=$i AND status > 0 AND domain='wap.28.com'")->count(); //特殊通道处理数
        }
        $this->assign('date', $date);
        $this->assign('aList', $aList);
        $this->display();
    }

    public function test() {
        $dDate = "2014-04-23";
        $aL = D("Guestbook")->where("deal_date='" . $dDate . "' AND send_status <> ''")->select();
        $aD = D("DataDealed")->where("addDate='" . $dDate . "'")->select();
        for ($i = 0; $i < count($aL); $i++) {
            echo $aL[$i]["ids"] . "\t" . $aL[$i]["phone"] . "\t" . $aL[$i]["send_status"] . "\t" . $aL[$i]["u_id"] . "----" . $aD[$i]["phone"] . "\t" . $aD[$i]["sendStatus"] . "\t" . $aD[$i]["u_id"] . "<br/>";
        }
        $this->display("index");
    }

}
