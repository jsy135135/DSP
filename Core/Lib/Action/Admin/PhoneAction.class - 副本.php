<?php
/*
 *根据返回数据情况，用读文件的形式，查询所处理的u_id;
 *可以进行做一些批量查询的工作
 *
 */
	class PhoneAction extends Action{
		public function index(){
			$data = M("data_dealed");
			$g = M("guestbook");
			$file = fopen("./6-27phone.txt","r") or exit("doudou");
			// $phone = fgets($file);
			// echo $phone;
			for ($i=0; $i<101; $i++) {
				$phone = fgets($file);
				$phoneT = trim($phone);
				// var_dump($phoneT);
				// echo '<br />';
				$Data = $data->where("phone = '".$phoneT."'")->getField('u_id');
				// $Gata = $g->where("phone = '".$phoneT."'")->getField('u_id');
				// $ids = fgets($file);
				// $g = M("guestbook");
				// $Uid = $g->where("ids = '".$ids."'")->getField("u_id");
				// $Data = $data->query("select * from where phone = '$phone'");
				// echo $data->getLastSql();
				// echo $i.' ';
				// echo $phoneT.' ';
				echo $Data.' ';
				echo $Gata;
				echo '<br />';
                                // echo $i.'&nbsp&nbsp&nbsp&nbsp';
				// echo $ids.'&nbsp&nbsp&nbsp&nbsp';
				// echo $Uid.'<br/>';
			}
			fclose($file);
		}
	}