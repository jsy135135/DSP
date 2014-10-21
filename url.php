<?php
$url = 'http://super.28.com/soap/dsp_400_send/send.php';
// getWebData($url);
getdata($url);
/**
 *
 *  GET获取远程端的数据
 *  @param String $url HTTP协议的URL地址
 *  @return Minxed
 *
 *
 */
  function getWebData($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    echo $output;
  }
  function getdata($url){
    $content = file_get_contents($url);
    // echo $content;
    $ar = unserialize ($content);
    var_dump($ar);
  }