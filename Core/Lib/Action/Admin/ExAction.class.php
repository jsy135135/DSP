<?php

/*
 * 一段时间内的相关留言信息的导出~~~
 * 特定类别
 *
 */

class ExAction extends Action {

    public function sourcelist() {
        $guestbook = M("guestbook");
        $data = $guestbook->query("SELECT add_date,source,count(*) as t  FROM `guestbook` WHERE `add_date` >= '2015-07-01' AND `add_date` <='2015-07-14' group by source,add_date order by add_date asc,source asc");
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            if ($data[$i]['source'] == '0') {
                $data[$i]['source'] = '旧来源';
            } elseif ($data[$i]['source'] == '1') {
                $data[$i]['source'] = '新来源';
            }
        }
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 日期);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 来源);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 数量);
//        $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 所属站点);
//        $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, 时间);
        for ($i = 0; $i < $datacount; $i++) {
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["add_date"]);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["source"]);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["t"]);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
//            $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $data[$i]["times"]);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("./pro/laiyuanlist" . ".xls");
        echo '导出成功';
        echo $datacount;
        echo '输出文件：' . $shu . '<br />';
    }

    public function danteng() {
        $data_deal = M("data_deal");
        $data = $data_deal->query("SELECT d.addDate,g.source,count(d.`phone`) as t  FROM `data_dealed` as d left join `guestbook` as g on d.phone = g.phone WHERE d.`addDate` >= '2015-07-01' AND d.`addDate` <='2015-07-13' AND d.regular = 1 AND d.transfer = 1 AND d.status = 8 group by d.addDate,g.source");
        $datacount = count($data);
        for ($i = 0; $i < $datacount; $i++) {
            if ($data[$i]['source'] == NULL) {
                $data[$i]['source'] = '未知来源';
            } elseif ($data[$i]['source'] == '0') {
                $data[$i]['source'] = '旧来源';
            } elseif ($data[$i]['source'] == '1') {
                $data[$i]['source'] = '新来源';
            }
        }
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 日期);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 来源);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 数量);
//        $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 所属站点);
//        $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, 时间);
        for ($i = 0; $i < $datacount; $i++) {
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["addDate"]);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["source"]);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["t"]);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
//            $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $data[$i]["times"]);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("./pro/zhuanjielaiyuan" . ".xls");
        echo '导出成功';
        echo $datacount;
        echo '输出文件：' . $shu . '<br />';
    }

    public function chadianhua() {
        $Api = new ApiAction;
        $file = fopen("./phone.txt", "r");
        $data = array();
        $i = 0;
        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line);
            $data[$i]['phone'] = $line;
            $yys = $Api->index($line);
            $data[$i]['yys'] = $yys;
            $i++;
        }
        fclose($file);
//        var_dump($data);
        $data_char = serialize($data);
        file_put_contents('./sp.txt', $data_char, FILE_APPEND);
    }

    public function aliyuntoex() {
//        $startdate = date("Y-m-d 9:00:00", strtotime("1 days ago"));
//        $enddate = date("Y-m-d 9:00:00");
        $startdate = '2015-05-31 00:00:00';
        $enddate = '2015-06-07 00:00:00';
        $data_52 = M("guestbook")->where("times >= '" . $startdate . "' AND times <='" . $enddate . "' AND site='91'")->getField('phone', true);
        $data_52_count = count($data_52);
//        var_dump($data_52);
//        die();
        $sql = "select DISTINCT phone,r,time,id from gbook where time >= '" . $startdate . "' AND time <='" . $enddate . "' group by phone order by id";
        $data = M("gbook", "", "mysql://root:!@#kingbone$%^@182.92.150.169:3306/gbook")->query($sql);
        $datacount = count($data);
//        echo $datacount;
//        die();
        for ($i = 0; $i < $datacount; $i++) {
//            $sss = in_array($data[$i]['phone'],$data_52);
            if (in_array($data[$i]['phone'], $data_52)) {
                unset($data[$i]);
            }
        }
        $data = array_values($data);
        $datacount_new = count($data);
//        echo '时间段 ：'.$startdate.'-------'.$enddate.'<br />';
//        echo '去重后数据共有<font color="red">'.$datacount_new.'</font>条'.'<br />';
//        echo '原来数据有<font color="green">'.$data_52_count.'</font>条';
//        echo '<table><tr><th>号码</th><th>链接</th><th>时间</th><th>id</th></tr>';
        for ($i = 0; $i < $datacount; $i++) {
//            echo $data[$i]['phone'].'<br />';
//            echo "<tr><td>".$data[$i]['phone']."</td><td style='word-wrap:break-word;word-break:break-all;'>".$data[$i]['r']."</td><td>".$data[$i]['time']."</td><td>".$data[$i]['id']."</td></tr>";
            file_put_contents('./yysphone.txt', $data[$i]['phone'] . "\r", FILE_APPEND);
        }
        echo '</table>';
    }

//每月自动生成源数据量报表
    public function yuanshuju() {
        $project = M("guestbook");
        $date = date("Y-m-d");
//        $date = '2016-09-05';
        $time = strtotime($date);
        $startday = date('Y-m-01', strtotime(date('Y', $time) . '-' . (date('m', $time) - 1) . '-01'));
        $filedate = date('Y-m', strtotime("$startday"));
        $endday = date('Y-m-d', strtotime("$startday +1 month -1 day"));
        $sql = "SELECT add_date ,count(ids) total FROM `guestbook` WHERE `add_date` >= '" . $startday . "' AND `add_date` <= '" . $endday . "' group by add_date";
        $data = $project->query($sql);
//        var_dump($data);
        $datacount = count($data);
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 日期);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 数量);
        for ($i = 0; $i < $datacount; $i++) {
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["add_date"]);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["total"]);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("./data/excel/" . $filedate . iconv('utf-8', 'GBK', '月数据源具体数量') . ".xls");
    }

    public function index() {
        $project = M("guestbook");
        for ($a = 0; $a < 30; $a++) {
            if ($a == 0) {
                $begin = 0;
            } else {
                $begin = ($a * 10000);
            }
            $totol = 10000;
            $shu = ($a + 1);
            echo $shu;
            $SQL = "SELECT phone,province,project_id,site FROM `guestbook` WHERE `add_date` <= '2013-12-31'LIMIT $begin,$totol";
            $data = $project->query($SQL);
//                 var_dump($data);
//                 die();
            require_once APP_PATH . 'PHPExcel/PHPExcel.php';
            require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 号码);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 省市);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 项目ID);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 来源网站);
            for ($i = 0; $i < $datacount; $i++) {
                $j = $i + 2;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["phone"]);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["province"]);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["project_id"]);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
            }
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save("./Ex/ex" . $shu . ".xls");
            echo '导出成功';
            $datacount = count($data);
            echo $datacount;
            echo '输出文件：' . $shu . '<br />';
        }
    }

//    public function JSY() {
//        $g = M("guestbook");
//        //星期六儿童乐园的来源网址页面
//        $address = array('http://wap.kxjh88.com/195_6_7207.html','http://m.liansuo.com/p/index/102480.html',)
//        $data = $g->query("SELECT site,count(site) as t  FROM `guestbook` WHERE `add_date` <= '2013-12-31' group by site");
////        echo '<pre>';
////        var_dump($data);
//        $datacount = count($data);
//        for($i=0;$i<$datacount;$i++){
//           echo $data[$i]['site'].'&nbsp&nbsp&nbsp&nbsp'.$data[$i]['t'].'<br />';
//        }
//    }
#特殊客户的数据导出
    public function phone() {
        $project = M("log", "", "mysql://root:!@#kingbone$%^@182.92.150.169:3306/ggw");
//        $project = M("guestbook");
//        $SQL = "SELECT phone,address,project_id,site FROM `guestbook` WHERE `add_date` >= '2014-11-12' AND `deal_date` = '0000-00-00' ";
        $SQL = "select dirname,url,site from log where dirname not like '%ps%' AND dirname not like '%2%' group by dirname";
        $data = $project->query($SQL);
        $datacount = count($data);
        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 名称缩写);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 链接);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 所属网站);
//        $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 来源网站);
        for ($i = 0; $i < $datacount; $i++) {
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["dirname"]);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["url"]);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["site"]);
//            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("./pro/prolist" . ".xls");
        echo '导出成功';
        echo $datacount;
        echo '输出文件：' . $shu . '<br />';
    }

    public function park() {
        $project = M("guestbook");
        $SQL = "SELECT g.phone,p.name,g.project_id,g.site,g.times  FROM `guestbook` as g left  join project as p on g.project_id = p.projectID AND g.site = p.site WHERE `add_date` >= '2015-05-01' AND `add_date` <= '2015-06-20' AND g.project_id in (select projectID from project where `pid`=9)";
//        $SQL = "";
        $data = $project->query($SQL);
        $datacount = count($data);
//        var_dump($data);
        $data_dealed = M("data_dealed");
        $phone_dealed = $data_dealed->where("`addDate` >= '2015-05-01' AND `addDate` <= '2015-06-20' AND projectID in (select projectID from project where `pid`=9)")->getField("phone", true);
//        var_dump($phone_dealed);
        for ($i = 0; $i < $datacount; $i++) {
            if (in_array($data[$i]['phone'], $phone_dealed)) {
                unset($data[$i]);
            }
        }
        $data = array_values($data);
//        var_dump($data);
//        die();

        require_once APP_PATH . 'PHPExcel/PHPExcel.php';
        require_once APP_PATH . 'PHPExcel/PHPExcel/Writer/Excel5.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, 电话);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, 项目名称);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, 项目ID);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, 所属站点);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, 时间);
        for ($i = 0; $i < $datacount; $i++) {
            $j = $i + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, $data[$i]["phone"]);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, $data[$i]["name"]);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $data[$i]["project_id"]);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $data[$i]["site"]);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $data[$i]["times"]);
        }
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save("./pro/ertong" . ".xls");
        echo '导出成功';
        echo $datacount;
        echo '输出文件：' . $shu . '<br />';
    }

}
