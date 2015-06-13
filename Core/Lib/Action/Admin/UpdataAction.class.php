<?php

header("content-type:text/html;charset=utf-8");
/*
 * 作为清理数据的用途
 *
 */

class UpdataAction extends Action {

    //修改一个字段到另外一个字段的值
    /*
      public function up_update_date(){
      $add_date = '2015-05-16';
      $gb = M("guestbook");
      $data = $gb->where("add_date = '".$add_date."'")->getField("ids",true);
      $data = implode(',', $data);
      $rs = $gb->where("ids in ($data)")->setField('update_date',$add_date);
      echo $rs.'<br />';
      }
     */
    //传入需要分配旧数据的对象
    public function reindex() {
        $user = M('user');
        $gb = M("guestbook");
        $dDate = date("Y-m-d", strtotime("15 days ago"));
        $sql = "select ids from guestbook where deal_status<>7 AND deal_status <> 8 AND u_id <>666 AND u_id <> 888 AND update_date = '" . $dDate . "'";
        $dataArray = $gb->query($sql);
        $Nums = count($dataArray);
        $data = $user->where("role=5")->field('username,remark')->select();
        $this->assign('Nums', $Nums);
        $this->assign('data', $data);
        $this->assign('odate', $dDate);
//            var_dump($data);
        $this->display();
    }

    //收回规定日期的数据，规定状态的数据
    public function recycle() {
        $gb = M("guestbook");
        $TheDate = date("Y-m-d");
        $Thetime = date("Y-m-d H:i:s");
        $dDate = date("Y-m-d", strtotime("15 days ago"));
        $Arr = $_REQUEST["arr"];
        #参与分配的详细工号数组
        $arr = explode(",", $Arr);
        #参与分配的人数
        $arrcount = count($arr);
        //查询共有多少可分配旧数据
        $sql = "select ids from guestbook where deal_status<>7 AND deal_status <> 8 AND u_id <>666 AND u_id <> 888 AND update_date = '" . $dDate . "'";
        $dataArray = $gb->query($sql);
        $Nums = count($dataArray);
//        var_dump($dataArray);
//        die();
        echo '回收旧现有数据：' . $Nums . '<br />';
        echo '有员工数量：' . $arrcount . '<br />';
        $iNowNum = floor($Nums / $arrcount);
        echo '每人分配：' . $iNowNum . '<br />';
//        die();
        //通过分配人数,决定分配多少次，原则上是每人一次
        for($i = 0; $i < $arrcount; $i++){
            $uID = $arr["$i"];
//            echo $uID;
            //按照每人分配数量，进行数据ids的提取
            $ssql = "select ids from guestbook where deal_status<>7 AND deal_status <> 8 AND u_id <>666 AND u_id <> 888 AND update_date = '" . $dDate . "'order by ips asc limit " . $iNowNum . "";
            $alist = $gb->query($ssql);
//            var_dump($alist);
            $alistcount = count($alist);
//            echo $alistcount;
            #进行组织获取分配到的数据id，重组数据，用来进行数据库的标记
            $idArray = "0";
            for ($j = 0; $j < $alistcount; $j++) {
                $idArray = $idArray . "," . $alist[$j]["ids"];
            }
             #进行数组重组，随后通过数据库进行标记
            $aData["deal_status"] = 1; //标注处理状态为1
            $aData["u_id"] = $uID; //当前分配用户的ID
            $aData["deal_time"] = '00-00-00 00:00:00'; //处理时间重置为0
            $aData["update_date"] = $TheDate; //标注旧数据分配的日期
            $aData["Thetime"] = $Thetime; //分配的详细时间
            $aData["Thedate"] = $TheDate; //分配的日期
            $stu = $gb->where("ids in ($idArray)")->save($aData);
//            echo $idArray.'<br />';
             #日志文件记录数据分配情况
            $sFileName = "./Log/old_bak-" . $TheDate . ".txt";
            $fp = fopen($sFileName, "a+");
            fwrite($fp, date("Y-m-d H:i:s") . "#" . "分配到的数量：" . $iNowNum . "#" . $arr[$i] . "#" . $idArray . "\n");
            fclose($fp);
//            echo '工号'.$uIDarray[$i].'分配数据完毕';
        }
    }

    public function index() {
        $user = M('user');
        $data = $user->where("role=5")->field('username,remark')->select();
        $this->assign('data', $data);
//            var_dump($data);        
        $this->display();
    }

    /*
     * 收回一对一的数据
     * 
     * 
     */

    public function UP() {
        $gb = M("guestbook");
        $Arr = $_REQUEST["arr"];
        $arr = explode(",", $Arr);
        $arrCount = count($arr);
        echo $arrCount . '<br />';
        // die();
        for ($i = 0; $i < $arrCount; $i++) {
            // echo $arr["$i"];
            // }
            // die();
            // $uID = 808;
            $uID = $arr[$i];
            // echo $uID;
            // die();
            $aList = $gb->Distinct(true)->field('phone', 'ids')->where("project_id > 0  AND add_date>='" . $dDate . "' AND u_id = '" . $uID . "' AND deal_time='0000-00-00 00:00:00'")->order("ids asc")->select();
            $idArray = "0";
            $aListcount = count($aList);
            for ($j = 0; $j < $aListcount; $j++) {
                $idArray = $idArray . "," . $aList[$j]["ids"];
            }
            $aData["u_id"] = 0;
            $gb->where("ids in ($idArray)")->save($aData);
            echo $uID . " updata succsess" . '<br />';
        }
        echo "updata all succsess";
    }

    public function BEUPD() {
        $user = M('user');
        $data = $user->where("role=5")->field('username,remark')->select();
        $this->assign('data', $data);
//            var_dump($data);        
        $this->display(DD);
    }

    /*
     * 收回一对多的数据
     * 
     * 
     */

    public function UPD() {
        $gb = M("guestbook");
        $Arr = $_REQUEST["arr"];
        $arr = explode(",", $Arr);
        $arrCount = count($arr);
        echo $arrCount . '<br />';
        // die();
        for ($i = 0; $i < $arrCount; $i++) {
            // echo $arr["$i"];
            // }
            // die();
            // $uID = 808;
            $uID = $arr[$i];
            // echo $uID;
            // die();
            $aList = $gb->Distinct(true)->field('phone', 'ids')->where("project_id = 0  AND add_date>='" . $dDate . "' AND u_id = '" . $uID . "' AND deal_time='0000-00-00 00:00:00'")->order("ids asc")->select();
            $idArray = "0";
            $aListcount = count($aList);
            for ($j = 0; $j < $aListcount; $j++) {
                $idArray = $idArray . "," . $aList[$j]["ids"];
            }
            $aData["u_id"] = 0;
            $gb->where("ids in ($idArray)")->save($aData);
            echo $uID . " updata succsess" . '<br />';
        }
        echo "updata all succsess";
    }

    public function upaim() {
        $user = M("user");
        for ($i = 801; $i <= 850; $i++) {
            $user->where("username = '" . $i . "'")->setField('aim', 0);
            echo $i . ' is delete' . '<br />';
        }
        echo '今日目标全部清理完毕';
    }

}
