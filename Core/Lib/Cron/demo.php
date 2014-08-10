<?php
//计划执行的文件
$fp= fopen("test.txt", "w");
if($fp) { 
	$count=0; 
	for($i=1;$i<=4;$i++){ 
		$flag=fwrite($fp,"行".$i." : "."Hello World!\r\n"); 
		if(!$flag){
			echo"写入文件失败<br>";break;         
		}
		$count+=$flag; 
	} 
}
fclose($fp);