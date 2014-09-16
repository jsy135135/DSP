<?php
/*
 *一段时间内的相关留言信息的导出~~~
 *特定类别
 *
 */
 class CanyinAction extends Action {
 	public function index(){
 		// $map['catName'] = array('like','餐饮%');
 		// $guestbook = M("guestbook");
 		$project = M("project");
 		// // $catnamelist = $project->where($map)->select();
 		// $catnamelist = $project->where($map)->getField("projectID",true);
 		// $catnameCount = count($catnamelist);
 		// // var_dump($catnamelist);
 		// // echo $catnamelist[0];
 		// // $gbData = $guestbook->where("project_id = '".$catnamelist[0]."'"."AND add_date > '2014-01-01'"."AND add_date < '2014-05-01'")->select();
 		// // var_dump($gbData);
 		// for ($i=0; $i <$catnameCount; $i++) {
 		// 	$gbData = $guestbook->where("project_id = '".$catnamelist[$i]."'"."AND add_date > '2014-01-01'"."AND add_date < '2014-05-01'")->select();
 		// 	var_dump($gbData);
 		// }
 		// $gbData = $guestbook->where("add_date > '2014-01-01' AND add_date < '2014-05-01'")->select();
 		// var_dump($gbData);
 		$shu =4;
 		$SQL = "select * from project left join guestbook on project.projectID = guestbook.project_id where project.catName like '家居建材'and project.suBCat like '灯具' and guestbook.add_date > '2014-01-01' and guestbook.add_date < '2014-06-16' and guestbook.site = 'zf'";
 		$data = $project->query($SQL);
 		// $data = $project->query("select * from project left join guestbook on project.projectID = guestbook.project_id where project.catName like '餐饮%' and guestbook.add_date > '2014-01-01' and guestbook.add_date < '2014-05-01' limit 280000,10000");
 		// echo '<pre>';
 		 var_dump($data);
//                 echo json_encode($data);
                 die();
 		$datacount = count($data);
 		echo $datacount;
 		echo '输出文件：'.$shu;
 		// $str = serialize($data);            //数组转换成字符串
   //  	file_put_contents('file.txt',$str); //写入文件
 		// echo 'success';
 		// die();
 		// Vendor('PHPExcel_1.8.0_doc.Classes.PHPExcel');
 		// echo APP_PATH;
 		require_once APP_PATH.'PHPExcel/PHPExcel.php';
 		require_once APP_PATH.'PHPExcel/PHPExcel/Writer/Excel5.php';
 		$objPHPExcel = new PHPExcel();
 			$objPHPExcel->getActiveSheet()->setCellValue('A' . 1,项目大类);
 			$objPHPExcel->getActiveSheet()->setCellValue('B' . 1,项目子类);
 			$objPHPExcel->getActiveSheet()->setCellValue('C' . 1,项目名称);
 			$objPHPExcel->getActiveSheet()->setCellValue('D' . 1,电话);
 			// $objPHPExcel->getActiveSheet()->setCellValue('E' . 1,省市);
 			$objPHPExcel->getActiveSheet()->setCellValue('E' . 1,添加时间);
 			$objPHPExcel->getActiveSheet()->setCellValue('F' . 1,site);
 			$objPHPExcel->getActiveSheet()->setCellValue('G' . 1,send_status);
 		for ($i=0; $i <$datacount ; $i++) {
 			$j = $i+2;
 			$objPHPExcel->getActiveSheet()->setCellValue('A' . $j,$data[$i]["catName"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('B' . $j,$data[$i]["subCat"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('C' . $j,$data[$i]["name"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('D' . $j,$data[$i]["phone"]);
 			// $objPHPExcel->getActiveSheet()->setCellValue('E' . $j,$data[$i]["province"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('E' . $j,$data[$i]["add_date"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('F' . $j,$data[$i]["site"]);
 			$objPHPExcel->getActiveSheet()->setCellValue('G' . $j,$data[$i]["send_status"]);
 		}
 		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
 		$objWriter->save("./dengshi/dengshi".$shu.".xls");
 		echo '导出成功';
 	}
 }