<?php
/**
 * xml文件转化为数组
 * @param unknown $url
 * @param number $get_attributes
 * @param string $priority
 * @return void|multitype:
 */
function xml2array($url, $get_attributes = 1, $priority = 'tag')
{
	$contents = "";
	if (!function_exists('xml_parser_create'))
	{
		return array ();
	}
	$parser = xml_parser_create('');
	if (!($fp = @ fopen($url, 'rb')))
	{
		return array ();
	}
	while (!feof($fp))
	{
		$contents .= fread($fp, 8192);
	}
	fclose($fp);
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	if (!$xml_values)
		return; //Hmm...
	$xml_array = array ();
	$parents = array ();
	$opened_tags = array ();
	$arr = array ();
	$current = & $xml_array;
	$repeated_tag_index = array ();
	foreach ($xml_values as $data)
	{
		unset ($attributes, $value);
		extract($data);
		$result = array ();
		$attributes_data = array ();
		if (isset ($value))
		{
			if ($priority == 'tag')
				$result = $value;
			else
				$result['value'] = $value;
		}
		if (isset ($attributes) and $get_attributes)
		{
			foreach ($attributes as $attr => $val)
			{
				if ($priority == 'tag')
					$attributes_data[$attr] = $val;
				else
					$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
			}
		}
		if ($type == "open")
		{
			$parent[$level -1] = & $current;
			if (!is_array($current) or (!in_array($tag, array_keys($current))))
			{
				$current[$tag] = $result;
				if ($attributes_data)
					$current[$tag . '_attr'] = $attributes_data;
				$repeated_tag_index[$tag . '_' . $level] = 1;
				$current = & $current[$tag];
			}
			else
			{
				if (isset ($current[$tag][0]))
				{
					$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
					$repeated_tag_index[$tag . '_' . $level]++;
				}
				else
				{
					$current[$tag] = array (
							$current[$tag],
							$result
					);
					$repeated_tag_index[$tag . '_' . $level] = 2;
					if (isset ($current[$tag . '_attr']))
					{
						$current[$tag]['0_attr'] = $current[$tag . '_attr'];
						unset ($current[$tag . '_attr']);
					}
				}
				$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
				$current = & $current[$tag][$last_item_index];
			}
		}
		elseif ($type == "complete")
		{
			if (!isset ($current[$tag]))
			{
				$current[$tag] = $result;
				$repeated_tag_index[$tag . '_' . $level] = 1;
				if ($priority == 'tag' and $attributes_data)
					$current[$tag . '_attr'] = $attributes_data;
			}
			else
			{
				if (isset ($current[$tag][0]) and is_array($current[$tag]))
				{
					$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
					if ($priority == 'tag' and $get_attributes and $attributes_data)
					{
						$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag . '_' . $level]++;
				}
				else
				{
					$current[$tag] = array (
							$current[$tag],
							$result
					);
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $get_attributes)
					{
						if (isset ($current[$tag . '_attr']))
						{
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset ($current[$tag . '_attr']);
						}
						if ($attributes_data)
						{
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
				}
			}
		}
		elseif ($type == 'close')
		{
			$current = & $parent[$level -1];
		}
	}
	return ($xml_array);
}

/**
 * 数组排序
 * @param unknown $arr
 * @param unknown $keys
 * @param string $type
 * @return multitype:unknown
 */
function array_sort($arr,$keys,$type='asc'){
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array;
}

/**
 * 模拟发送，post模式
 * @param unknown $remote_server
 * @param unknown $post_string
 * @return mixed
 */
function request_by_curl($remote_server, $post_string)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote_server);
	curl_setopt($ch, CURLOPT_POSTFIELDS,  $post_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "auto post");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
/*
 * curl封装函数
 * 
 */
function curls($url, $timeout = '10') {
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 3. 执行并获取HTML文档内容
        $info = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);

        return $info;
    }
/*
 * curl通过POST方式发送数据
 * 
 */
function curl_post($url,$post){
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
    $return = curl_exec ( $ch );
    curl_close ( $ch );
    return $return;
}


// 测试写入文件
function testwrite($d){
	$tfile = 'cms.txt';
	$d = ereg_replace('/$','',$d);
	$fp = @fopen($d.'/'.$tfile,'w');
	if(!$fp){
		return false;
	}else{
		fclose($fp);
		$rs = @unlink($d.'/'.$tfile);
		if($rs){
			return true;
		}else{
			return false;
		}
	}
}
// 获取文件夹大小
function getdirsize($dir){ 
	$dirlist = opendir($dir);
	while (false !== ($folderorfile = readdir($dirlist))){ 
		if($folderorfile != "." && $folderorfile != "..") { 
			if (is_dir("$dir/$folderorfile")) { 
				$dirsize += getdirsize("$dir/$folderorfile"); 
			}else{ 
				$dirsize += filesize("$dir/$folderorfile"); 
			}
		}    
	}
	closedir($dirlist);
	return $dirsize;
}
function getrexie($str){
	return str_replace('@@@','',str_replace('/@@@','',$str.'@@@'));
}
function getaddxie($str){
	return str_replace('@@@','',str_replace('//@@@','/',$str.'/@@@'));
}