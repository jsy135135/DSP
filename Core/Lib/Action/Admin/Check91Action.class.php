<?php

/* 
 * 查看91dsp的每日详细流量
 * 
 */
class Check91Action extends Action {
    public function index(){
        $d = M("data_dealed");
        $startDate = $_REQUEST["startDate"] == "" ? date("Y-m-d") : $_REQUEST["startDate"];
        $endDate = $_REQUEST["endDate"] == "" ? date("Y-m-d") : $_REQUEST["endDate"];
        $sql = "SELECT d.projectID,count(*) as count,p.name FROM `data_dealed` as d left join project as p on d.projectID = p.projectID AND d.site = p.site WHERE d.site LIKE '91' AND d.check = 1 AND d.regular = 1 AND d.addDate <= '".$endDate."' AND d.addDate >= '".$startDate."' group by d.projectID";
        $data = $d->query($sql);
        $this->assign("startDate" , $startDate);
        $this->assign("endDate" , $endDate);
        $this->assign("data" , $data);
        $this->display();
    }
    public function vip(){
        $g = M("guestbook");
        $data = $g->where("address like '%vip_anhuizhonghua%'")->select();
        echo '<table align="center">';
        echo '<tr><td>电话</td><td>ip</td><td>来源网址</td><td>日期</td></tr>';
        foreach ($data as $key => $value) {
            echo '<tr>';
            echo '<td>'.$value['phone'].'</td>';
            echo '<td>'.$value['ips'].'</td>';
            echo '<td>'.$value['address'].'</td>';
            echo '<td>'.$value['add_date'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    //特殊客户查看dsp源数据(特定项目)
    public function dspforc_show(){
        $startDate = $_REQUEST["startDate"] == "" ? date("Y-m-d") : $_REQUEST["startDate"];
        $endDate = $_REQUEST["endDate"] == "" ? date("Y-m-d") : $_REQUEST["endDate"];
        $gb_c = M("gb_c");
        $user = M("user");
        $date = $date = date("Y-m-d");
        $project_id = $user->where("role = '13'")->getField("remark");
        $data = $gb_c->where("project_id = '".$project_id."' AND site = '91' AND add_date >= '".$startDate."'AND add_date <= '".$endDate."'")->select();
        $datacount = count($data);
        $this->assign("startDate" , $startDate);
        $this->assign("endDate" , $endDate);
        $this->assign("data" , $data);
        $this->assign("datacount" ,$datacount);
        $this->display();
    }
}
