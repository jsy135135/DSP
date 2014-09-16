<?php
/*
 *根据返回数据情况，用读文件的形式，查询所处理的u_id;
 *可以进行做一些批量查询的工作
 *
 */
	class PhoneAction extends Action{
		public function index(){
			$data = M("project");
			$file = fopen("./pro.txt","r") or exit("doudou");
			// $phone = fgets($file);
			// echo $phone;
                          echo '<table>';
			for ($i=0; $i<60; $i++) {
				$phone = fgets($file);
				$phoneT = trim($phone);
				// var_dump($phoneT);
				// echo '<br />';
//				$Data = $data->where("phone = '".$phoneT."'")->getField('name');
				// $Gata = $g->where("phone = '".$phoneT."'")->getField('u_id');
				// $ids = fgets($file);
				// $g = M("guestbook");
				// $Uid = $g->where("ids = '".$ids."'")->getField("u_id");
				// $Data = $data->query("select * from where phone = '$phone'");
				// echo $data->getLastSql();
				// echo $i.' ';
				// echo $phoneT.' ';
//				echo $Data.' ';
				$rs = $data->where("name = '".$phoneT."' AND site='ls'")->getField("projectID");
//                                var_dump($rs);
                                echo '<tr>';
                                echo '<td>';
                                echo $phoneT;
                                echo '</td>';
                                echo '<td>';
                                echo $rs;
//                                var_dump($rs);
//                                die();
                                echo '</td>';
                                echo '</tr>';
                              
			}
                          echo '</table>';
			fclose($file);
		}
	}