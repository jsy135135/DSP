<?php

/* 
 * 呼叫中心目标模块
 * 
 * @siyuan 
 */
    class AMyAimAction extends OQAction{
        public function index(){
            $uID = session("username");
            if($uID == "admin"){
                $uID = 826; //默认外呼标记为826
            }
            $date = date("Y-m-d");
            $user = M("user");
            $dealed = M("data_dealed");
            $again = M("data_again");
            $aim = $user->where("username = '".$uID."'")->getField("aim");//个人目标数
            $alnums = $dealed->where("addDate = '".$date."' AND u_id = '".$uID."' AND status>0")->count("DISTINCT phone");//个人一次发送成功数
            $againSubmitNum = $again->where("add_date =  '".$date."' AND u_id=$uID AND status > 0")->count("DISTINCT  data_id");//个人二次发送成功数
            $allnums = $dealed->where("addDate = '".$date."' AND status>0")->count("DISTINCT phone");//团队一次发送成功数
            $allagainSubmitNum = $again->where("add_date =  '".$date."' AND status > 0")->count("DISTINCT  data_id");//团队二次发送成功数
            $allnums = ($allnums+$allagainSubmitNum);
            $nums = ($aim-$alnums);//个人距离数
            $allalnums = ($allaim-$allnums);//团队距离数
            $ssendnums = -$nums;
            if($nums < 0){
                $nums = 0;
            }
            $allaim = $user->where("username = 'weitao'")->getField("aim");
            $allalnums = ($allaim-$allnums);//
            $alnums = ($alnums+$againSubmitNum);
            $userrole = 1;
            if($uID == 'weitao'){
                $userrole = 0;
            }
            $this->assign("userrole",$userrole);
            $this->assign("aim",$aim);
            $this->assign("alnums",$alnums);
            $this->assign("nums",$nums);
            $this->assign("ssendnums",$ssendnums);
            $this->assign("allaim",$allaim);
            $this->assign("allalnums",$allalnums);
            $this->assign("allnums",$allnums);
            $this->display();
        }
        public function edit(){
           $this->display();
        }

        public function updata(){
            $aim = $_REQUEST["aim"];
            $teamaim = $_REQUEST["teamaim"];
            $user = M("user");
            $user->where("username = 'weitao'")->setField('aim',$teamaim);
            for($i=801;$i<=850;$i++){
            $data['aim'] = $aim;
            $rs = $user->where("username = '".$i."'")->save($data);
            }
                echo '填写今天目标成功';
        }
    }

