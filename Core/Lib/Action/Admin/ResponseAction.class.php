<?php

/*
 * 当日源数据，快速返回接口
 * time:2015年6月30日15:02:44
 * By:siyuan
 */

class ResponseAction extends Action {
    
    public function index() {
        header("content-type:text/html; charset=UTF-8"); 
        $startday = $_REQUEST['startdate'] == "" ? date("Y-m-d") : $_REQUEST['startdate'];
//        $startday = '2015-03-28';
        $endday = $_REQUEST['enddate'] == "" ? date("Y-m-d") : $_REQUEST['enddate'];
        $g = M("guestbook");
        $totol = $g->query("select add_date,count(*) as t from guestbook where add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //来源总量
        $Ytotol = $g->query("select add_date,count(*) as t from guestbook where project_id >0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1v1 来源总量
        $Ntotol = $g->query("select add_date,count(*) as t from guestbook where project_id = 0 AND add_date >= '" . $startday . "' AND add_date <= '" . $endday . "'group by add_date"); //1vN 来源总量
       echo '<style>font-size:28px</style>';
       echo '<h1>日期:'.$startday.'</h1><br />';
       echo '<h1>总量:'.$totol[0]['t'].'</h1><br />';
       echo '<h1>一对一:'.$Ytotol[0]['t'].'</h1><br />';
       echo '<h1>一对多:'.$Ntotol[0]['t'].'</h1><br />';
    }

}
