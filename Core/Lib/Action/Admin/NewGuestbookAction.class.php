<?php

//客户信息的处理
class NewGuestbookAction extends OQAction {

    /**
     * 	给每个工号分配数据
     *
     * */
    function index() {
        $uID = session("username");
        if ($uID == "admin") {
            $uID = 826; //默认外呼标记为826
        }
        $dDate = date("Y-m-d", strtotime("30 days ago"));  //昨天1 days ago
        $gb = M("guestbook");
        $aList = $gb->where("deal_status = 1 AND project_id = 0 AND u_id= " . $uID . " AND add_date>='" . $dDate . "' AND deal_time='0000-00-00 00:00:00' AND repeat_phone = 0")->order("Thetime desc")->select();
        $iNowNum = count($aList);
        #加入推荐的栏目列表
        $aBigList = $this->_listCategory(1);
        $subList = D("Category")->where("pid != 0")->select();

        $this->assign("uID", $uID);
        $this->assign("nowAssignNum", $iNowNum);
        $this->assign("aList", $aList);
        #栏目列表传到页面
        $this->assign("aBigList", $aBigList);
        $this->assign("subList", $subList);
        $this->display();
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
     * ajax获取可发送的推荐项目
     */
    public function getNewProject($newCid = null) {
        $Category = M("Category");
        $project = M("Project");
        #查出推荐的项目名称和链接地址
        $list = $project->where("cid = '$newCid' AND status > 0 AND sendStatus>0 AND level >= 4 AND numbers > 0")->getField('projectID,name,webPage,site');
        echo json_encode($list);
    }
    /*
     * 获取到单个项目的抓取数量
     */

    function getNumByPID($iPID, $dDate, $sSite = "28") {
        $gb = M("Guestbook");
        $iCount = $gb->where("project_id=" . $iPID . " AND site='" . $sSite . "' AND add_date='" . $dDate . "'")->count();
        return $iCount;
    }
}
    