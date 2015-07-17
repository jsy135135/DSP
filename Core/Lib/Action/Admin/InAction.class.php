<?php

header("content-type:text/html;charset=utf-8");
/*
 * 数据定量的分配
 *
 */

class InAction extends Action {

    public function index() {
        $user = M('user');
        $data = $user->where("role=5")->field('username,remark')->select();
        $this->assign('data', $data);
//            var_dump($data);
        $this->display();
    }

    public function TFP() {
        $this->display();
    }
    /*
     * 28特定项目的，特定数量处理
     * Time: 2015-06-11 15:10:15
     * By:siyuan
     */
    
    public function jialiang28(){
        $gb = M("guestbook");
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $dDate = date("Y-m-d", strtotime("2 days ago"));
        $uID = '8028';
        $sql = "SELECT *  FROM `guestbook` WHERE `project_id`  in(8400,8485,8483,7182,8462,7440,8366,8477,7555,5788,5543,8447,6436,8385) AND add_date >= '".$dDate."' AND u_id<>8028 AND deal_status not in (7,8) AND site='28'";
        
        $dataArray = $gb->query($sql);
        $Nums = count($dataArray);
        echo '28加量业务现有数据：' . $Nums . '<br />'; //查看特殊现有的数量
        #对分配过的数据在guestbook表中进行标注
        $aData["deal_status"] = 1;
        $aData["u_id"] = $uID;
        #记录每条数据的分配时间
        $aData["Thetime"] = $Thetime;
        $aData["Thedate"] = $TheDate;
        #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
        $idArray = "0";
        for ($j = 0; $j < $Nums; $j++) {
            $idArray = $idArray . "," . $dataArray[$j]["ids"];
        }
        $stu = $gb->where("ids in ($idArray)")->save($aData);
         #日志文件记录数据分配情况
        $sFileName = "./Log/28jialiang-" . $TheDate . ".txt";
        $fp = fopen($sFileName, "a+");
        fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $Nums . "#" . $uID . "#" . $idArray . "\n");
        fclose($fp);
//            echo $gb->getLastsql();
        var_dump($stu);
        echo '28加量业务分配数据完毕';
    }

    /*
     * 查询特殊项目的链接，分配给特点的业务账号查看
     * Time：2015-4-6 11:54:21
     * By：siyuan
     * 目前项目为考拉大冒险项目
     */

    public function TFP_kaola() {
        $gb = M("guestbook");
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $dDate = date("Y-m-d", strtotime("2 days ago"));
        $uID = '666';
//        $sql = "SELECT *  FROM `guestbook` WHERE ((`project_id` = 550 AND site = '91') or (`project_id` = 289 AND site = '91') or (`project_id` = 137334 AND site = 'ls') or (`project_id` = 146179 AND site = 'ls') or (`project_id` = 7207 AND `site` LIKE '28') or (`project_id` = 1743 AND `site` LIKE 'zf') or (`project_id` = 47697 AND `site` LIKE 'ls') or (`project_id` = 6633 AND `site` LIKE '28') or (`project_id` = 7091 AND `site` LIKE '28') or (`project_id` = 7720 AND `site` LIKE '28') or (`project_id` = 2233 AND `site` LIKE 'zf') or (`project_id` = 7778 AND `site` LIKE '28') or (`project_id` = 138226 AND `site` LIKE 'ls') or (`project_id` = 7539 AND `site` LIKE 'wp') or (`project_id` = 7519 AND `site` LIKE 'wp') or (`project_id` = 305 AND `site` LIKE '91') or (`project_id` = 2356 AND `site` LIKE 'zf') or (`project_id` = 6644 AND `site` LIKE '28') or (`project_id` = 490 AND `site` LIKE '91')) AND add_date >= '" . $dDate . "' AND deal_time = '0000-00-00 00:00:00' AND deal_status = 0";
        $sql = "SELECT *  FROM `guestbook` WHERE ((`project_id` = 459 AND `site` LIKE '91') or (`project_id` = 460 AND `site` LIKE '91') or (`project_id` = 461 AND `site` LIKE '91') or (`project_id` = 462 AND `site` LIKE '91') or (`project_id` = 463 AND `site` LIKE '91') or (`project_id` = 464 AND `site` LIKE '91') or (`project_id` = 467 AND `site` LIKE '91') or (`project_id` = 138675 AND `site` LIKE 'ls') or (`project_id` = '2241' AND `site` LIKE 'zf') or (`project_id` = 465 AND site = '91') or (`project_id` = 687 AND site = '91') or (`project_id` = 1606 AND site = 'zf') or (`project_id` = 7460 AND site = 'wp') or (`project_id` = 145186 AND site = 'ls') or (`project_id` = 98314 AND site = 'ls') or (`project_id` = 2137 AND site = 'zf') or (`project_id` = 1288 AND site = 'zf') or (`project_id` = 146947 AND site = 'ls') or (`project_id` = 146714 AND site = 'ls') or (`project_id` = 135385 AND site = 'ls') or (`project_id` = 102480 AND site = 'ls') or (`project_id` = 7622 AND site = '28') or (`project_id` = 550 AND site = '91') or (`project_id` = 289 AND site = '91') or (`project_id` = 137334 AND site = 'ls') or (`project_id` = 146179 AND site = 'ls') or (`project_id` = 7207 AND `site` LIKE '28') or (`project_id` = 1743 AND `site` LIKE 'zf') or (`project_id` = 47697 AND `site` LIKE 'ls') or (`project_id` = 6633 AND `site` LIKE '28') or (`project_id` = 7091 AND `site` LIKE '28') or (`project_id` = 7720 AND `site` LIKE '28') or (`project_id` = 2233 AND `site` LIKE 'zf') or (`project_id` = 7778 AND `site` LIKE '28') or (`project_id` = 138226 AND `site` LIKE 'ls') or (`project_id` = 7539 AND `site` LIKE 'wp') or (`project_id` = 7519 AND `site` LIKE 'wp') or (`project_id` = 305 AND `site` LIKE '91') or (`project_id` = 2356 AND `site` LIKE 'zf') or (`project_id` = 6644 AND `site` LIKE '28') or (`project_id` = 490 AND `site` LIKE '91')) AND add_date >= '" . $dDate . "' AND u_id <> 666 AND deal_status not in (7,8)";
        $dataArray = $gb->query($sql);
        $Nums = count($dataArray);
        echo '特殊业务现有数据：' . $Nums . '<br />'; //查看特殊现有的数量
        #对分配过的数据在guestbook表中进行标注
        $aData["deal_status"] = 1;
        $aData["u_id"] = $uID;
        #记录每条数据的分配时间
        $aData["Thetime"] = $Thetime;
        $aData["Thedate"] = $TheDate;
        #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
        $idArray = "0";
        for ($j = 0; $j < $Nums; $j++) {
            $idArray = $idArray . "," . $dataArray[$j]["ids"];
        }
        $stu = $gb->where("ids in ($idArray)")->save($aData);
         #日志文件记录数据分配情况
        $sFileName = "./Log/kaola-" . $TheDate . ".txt";
        $fp = fopen($sFileName, "a+");
        fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $Nums . "#" . $uID . "#" . $idArray . "\n");
        fclose($fp);
//            echo $gb->getLastsql();
        var_dump($stu);
        echo '特殊业务分配数据完毕';
    }

    /*
     * 查询特殊项目的链接，分配给特点的业务账号查看
     * Time：2015年3月12日13:35:32
     * By：siyuan
     * 目前项目为雪之丘冰激凌项目
     */

    public function TFP_xzq() {
        $gb = M("guestbook");
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $dDate = date("Y-m-d", strtotime("2 days ago"));
        $uIDarray = array('888');
        $uIDcount = count($uIDarray);
//        $sql = "select ids from guestbook where (address like '%xzq_$t2%' or address like '%xzqjj%' or address like '%xzqbql-m%') AND deal_time = '0000-00-00 00:00:00' AND deal_status = 0 AND u_id <> 888 AND add_date >= '".$dDate."'";
        $sql = "select ids from guestbook where (address like '%xzqjj%' or address like '%xzqbql-m%') AND deal_time = '0000-00-00 00:00:00' AND deal_status = 0 AND u_id <> 888 AND add_date >= '".$dDate."'";
        $dataArray = $gb->query($sql);
        $Nums = count($dataArray);
        echo '特殊业务现有数据：' . $Nums . '<br />'; //查看特殊现有的数量
        echo '有员工数量：' . $uIDcount . '<br />';
        $iNowNum = floor($Nums/$uIDcount);
        echo '每人分配：' . $iNowNum . '<br />';
        for ($i = 0; $i < $uIDcount; $i++) {
            $ssql = "select ids from guestbook where (address like '%xzq_$t2%' or address like '%xzqjj%' or address like '%xzqbql-m%') AND deal_time = '0000-00-00 00:00:00' AND deal_status = 0 AND u_id <> 888 AND add_date >= '".$dDate."' order by ips asc limit ".$iNowNum."";
            $alist = $gb->query($ssql);
            $alistcount = count($alist);
            #对分配过的数据在guestbook表中进行标注
            $aData["deal_status"] = 1;
            $aData["u_id"] = $uIDarray[$i];
            #记录每条数据的分配时间
            $aData["Thetime"] = $Thetime;
            $aData["Thedate"] = $TheDate;
            #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
            $idArray = "0";
            for ($j = 0; $j < $alistcount; $j++) {
                $idArray = $idArray . "," . $alist[$j]["ids"];
            }
            $stu = $gb->where("ids in ($idArray)")->save($aData);
            #日志文件记录数据分配情况
            $sFileName = "./Log/xzq-" . $TheDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $iNowNum . "#" . $uIDarray[$i] . "#" . $idArray . "\n");
            fclose($fp);
//            echo $gb->getLastsql();
            var_dump($stu);
            echo '特殊业务分配数据完毕';
        }
    }

    /*
     * 计算一对一的源数据量，并进行批量的分配
     *
     */

    public function FP() {
        // var_dump($_REQUEST);
        $Arr = $_REQUEST["arr"];
        // var_dump($Arr);
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $gb = M("guestbook");
        $dDate = date("Y-m-d", strtotime("15 days ago"));
        $Nums = $gb->where("project_id > 0 AND deal_status = 0 AND u_id=0 AND add_date>='" . $dDate . "'AND deal_time='0000-00-00 00:00:00'")->count();
        echo '现有数据：' . $Nums . '<br />'; //查看一对一现有的数量
        #每天需要定量分配数据的客服号码
        $arr = explode(",", $Arr);
        // $arr = array($Arr);
        // var_dump($arr);
        // die();
        #通过人数和数据量，计算出没人应分配的条数
        $arrCount = count($arr);
        echo '客服人数：' . $arrCount . '<br />';
        #向下取整，保证为整数
        $iNowNum = floor($Nums / $arrCount);
        echo '每人分配：' . $iNowNum . '<br />';
        #一对一现有数据详细
        // $aList = $gb->Distinct(true)->field('phone','ids')->where("project_id > 0  AND add_date>='".$dDate."' AND u_id=0 AND deal_time='0000-00-00 00:00:00'")->limit($Nums)->order("phone asc")->select();
        // echo '本次取出数据：'.count($aList);
        // echo '<pre>';
        // var_dump($aList);
        // die();
        for ($i = 0; $i < $arrCount; $i++) {
            $uID = $arr["$i"];
            // $uID = 814;
            // $iNowNum = 20;
            $aList = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0 AND deal_status = 0 AND add_date>='" . $dDate . "' AND u_id=0 AND deal_time='0000-00-00 00:00:00'")->limit($iNowNum)->order("ips asc")->select();
            $aListCount = count($aList);
            echo $uID . '分配到的数量：' . $aListCount . '<br />';
            // echo $uID.'分配到的数量：'.$aListCount;
            #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
            $idArray = "0";
            for ($j = 0; $j < $aListCount; $j++) {
                $idArray = $idArray . "," . $aList[$j]["ids"];
            }
            #日志文件记录数据分配情况
            $sFileName = "./Log/SYDY-" . $TheDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $aListCount . "#" . $uID . "#" . $idArray . "\n");
            fclose($fp);
            #对分配过的数据在guestbook表中进行标注
            $aData["deal_status"] = 1;
            $aData["u_id"] = $uID;
            #记录每条数据的分配时间
            $aData["Thetime"] = $Thetime;
            $aData["Thedate"] = $TheDate;
            $gb->where("ids in ($idArray)")->save($aData);
        }
        echo '一对一数据分配完毕！';
    }

    public function BEFPD() {
        $user = M('user');
        $data = $user->where("role=5")->field('username,remark')->select();
        $this->assign('data', $data);
        $this->display(DD);
    }

    /*
     * 一对多分配方法
     * 
     * 
     */

    public function FPD() {
        // var_dump($_REQUEST);
        $Arr = $_REQUEST["arr"];
        // var_dump($Arr);
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $gb = M("guestbook");
        $dDate = date("Y-m-d", strtotime("15 days ago"));
        $Nums = $gb->where("project_id = 0 AND deal_status = 0 AND u_id=0 AND add_date>='" . $dDate . "'AND deal_time='0000-00-00 00:00:00'")->count();
        echo '现有数据：' . $Nums . '<br />'; //查看一对一现有的数量
        #每天需要定量分配数据的客服号码
        $arr = explode(",", $Arr);
        // $arr = array($Arr);
        // var_dump($arr);
        // die();
        #通过人数和数据量，计算出没人应分配的条数
        $arrCount = count($arr);
        echo '客服人数：' . $arrCount . '<br />';
        #向下取整，保证为整数
        $iNowNum = floor($Nums / $arrCount);
        echo '每人分配：' . $iNowNum . '<br />';
        #一对一现有数据详细
        // $aList = $gb->Distinct(true)->field('phone','ids')->where("project_id > 0  AND add_date>='".$dDate."' AND u_id=0 AND deal_time='0000-00-00 00:00:00'")->limit($Nums)->order("phone asc")->select();
        // echo '本次取出数据：'.count($aList);
        // echo '<pre>';
        // var_dump($aList);
        // die();
        for ($i = 0; $i < $arrCount; $i++) {
            $uID = $arr["$i"];
            // $uID = 814;
            // $iNowNum = 20;
            $aList = $gb->Distinct(true)->field('phone', 'ids')->where("project_id = 0 AND deal_status = 0 AND add_date>='" . $dDate . "' AND u_id=0 AND deal_time='0000-00-00 00:00:00'")->limit($iNowNum)->order("ips asc")->select();
            $aListCount = count($aList);
            echo $uID . '分配到的数量：' . $aListCount . '<br />';
            // echo $uID.'分配到的数量：'.$aListCount;
            #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
            $idArray = "0";
            for ($j = 0; $j < $aListCount; $j++) {
                $idArray = $idArray . "," . $aList[$j]["ids"];
            }
            #日志文件记录数据分配情况
            $sFileName = "./Log/SYDD-" . $TheDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $aListCount . "#" . $uID . "#" . $idArray . "\n");
            fclose($fp);
            #对分配过的数据在guestbook表中进行标注
            $aData["deal_status"] = 1;
            $aData["u_id"] = $uID;
            #记录每条数据的分配时间
            $aData["Thetime"] = $Thetime;
            $aData["Thedate"] = $TheDate;
            $gb->where("ids in ($idArray)")->save($aData);
        }
        echo '一对多数据分配完毕！';
    }

    /*
     * 查看分配后的，每人的详细数量
     *
     *
     */

    public function showlist() {
        $gb = M("guestbook");
        for ($i = 801; $i <= 850; $i++) {
            $dDate = date("Y-m-d", strtotime("2 days ago"));
            $date = date("Y-m-d");
            $count = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0  AND add_date>='" . $dDate . "' AND u_id=$i AND deal_status = 1 AND deal_time='0000-00-00 00:00:00'")->order("ids asc")->count();
            echo $i . '##' . $count . '<br />';
        }
    }

    /*
     *
     * 收集二次数据，再次进行分配
     *
     */

    public function showagain() {
        $gb = M("guestbook");
        $dDate = date("Y-m-d", strtotime("2 days ago"));
        $date = date("Y-m-d");
        $arr = array(801, 802, 804, 805, 808, 809, 810, 811, 813, 814, 815, 816, 818, 819, 820, 821, 822, 823, 826, 827, 828, 829, 831, 832, 833, 834, 835, 836, 838, 839, 845, 849);
        $arrCount = count($arr);
        echo '客服人数：' . $arrCount . '<br />';
        $Nums = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0  AND add_date>='" . $dDate . "' AND add_date != '" . $date . "' AND deal_status<>0 and deal_status <>7 and deal_status <>1 ")->order("phone desc,ips asc")->count();
        echo $Nums;
        $iNowNum = floor($Nums / $arrCount);
        echo '每人分配：' . $iNowNum . '<br />';
        echo '<pre>';
        echo '共有数据' . $count = count($aList);
        echo $uID . '###' . $count;
        // die();
        for ($i = 0; $i < $arrCount; $i++) {
            $uID = $arr["$i"];
            $uID = 814;
            $iNowNum = 89;
            $aList = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0  AND add_date>='" . $dDate . "' AND add_date != '" . $date . "' AND deal_status<>0 and deal_status <>7 and deal_status <>1 ")->limit($iNowNum)->order("phone desc,ips asc")->select();
            echo '<pre>';
            $count = count($aList);
            echo $uID . '###' . $count;
            // var_dump($aList);
            // die();
            $idArray = "0";
            $aListCount = count($aList);
            for ($j = 0; $j < $aListCount; $j++) {
                $idArray = $idArray . "," . $aList[$j]["ids"];
            }
            //记录数据分配情况
            $dDate = date("Y-m-d");
            $sFileName = "./Log/SY-" . $dDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $aListCount . "#" . $uID . "#" . $idArray . "\n");
            fclose($fp);
            #对分配过的数据在guestbook表中进行标注
            $aData["deal_status"] = 1;
            $aData["u_id"] = $uID;
            #记录每条数据的分配时间
            $aData["Thetime"] = $Thetime;
            $aData["Thedate"] = $TheDate;
            $gb->where("ids in ($idArray)")->save($aData);
        }
        echo 'already and success';
    }

    /*
     *
     * 查看一对一的二次外呼的数量
     *
     *
     */

    public function showagainlist() {
        $gb = M("guestbook");
        for ($i = 801; $i <= 850; $i++) {
            $dDate = date("Y-m-d", strtotime("2 days ago"));
            $date = date("Y-m-d");
            $count = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0  AND add_date>='" . $dDate . "' AND u_id=$i AND deal_status = 1 AND deal_time='0000-00-00 00:00:00'")->order("ids asc")->count();
            echo $i . '##' . $count . '<br />';
        }
    }

}
