<?php
header("content-type:text/html;charset=utf-8");
/*
 *用来查询每天的提交负数的详细信息
 *
 */
class DuiAction extends OQAction{
	/*
	 *
	 *
	 */
	public function index($date = null,$site = null){
        $dDate = $_REQUEST["date"] == "" ?  date("Y-m-d") : $_REQUEST["date"];
        $site = $_REQUEST["site"] == "" ?  '28' : $_REQUEST["site"];
        $d = M("data_dealed");
        $p = M("project");
        $data = $d->where("status < 0 AND addDate = '$dDate' AND site = '".$site."'")->select();
        echo '<pre>';
        // var_dump($data);
        $count = count($data);
        for($i=0;$i<$count;$i++){
            echo '<font size="6">'.$data["$i"]["projectID"].'###'.$data["$i"]["status"].'###'.$data["$i"]["site"].'</font>';
                $projectID = $data["$i"]["projectID"];
                $site = $data["$i"]["site"];
                $pdata = $p->where("projectID = $projectID AND site = '".$site."'")->select();
                // var_dump($pdata);
                if(!empty($pdata)){
                    echo '<font size="6" color="red">'.$pdata["0"]["name"].$pdata["0"]["status"].'<br /></font>';
                }else{
                    echo '<font size="6">###N<br /></font>';
                }
            }
        // $datals = $d->where("status < 0 AND addDate = '2014-06-26' AND site = 'ls' ")->select();
        // var_dump($datals);
        // $datazf = $d->where("status < 0 AND addDate = '2014-06-26' AND site = 'zf' ")->select();
        // var_dump($datazf);
    }
}
