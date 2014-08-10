<?php

header("content-type:text/html;charset=utf-8");
/*
 * 作为清理数据的用途
 *
 */

class UpdataAction extends Action {

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
