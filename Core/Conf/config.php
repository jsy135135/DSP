<?php
/**
 * @name $config 系统配置文件
 */
$config = require './config.php';
$array = array(
		'APP_GROUP_LIST'       => 'Admin,Home',	// 分组
		'TMPL_FILE_DEPR'       => '_',			// 模板文件MODULE_NAME与ACTION_NAME之间的分割符，只对项目分组部署有效
		'DEFAULT_GROUP'        => 'Home', 		// 默认分组
		'URL_MODEL'            => 3,			// URL兼容模式
		'URL_CASE_INSENSITIVE' => true,			// URL是否不区分大小写 默认区分大小写
		'DB_FIELDTYPE_CHECK'   => true, 		// 是否进行字段类型检查
		'DATA_CACHE_SUBDIR'    => true,			// 哈希子目录动态缓存的方式
		'DATA_PATH_LEVEL'      => 2,
		'TMPL_STRIP_SPACE'     => false,		//是否去除模板文件里面的html空格与换行
		
		'TOKEN_ON'             => true,  // 是否开启令牌验证
		'TOKEN_NAME'           => '__hash__',    // 令牌验证的表单隐藏字段名称
		'TOKEN_TYPE'           => 'md5',  //令牌哈希验证规则 默认为MD5
		'TOKEN_RESET'          => true,  //令牌验证出错后是否重置令牌 默认为true

		'TMPL_ACTION_ERROR'    => './Public/tips/tips.html', // 默认错误跳转对应的模板文件
		'TMPL_ACTION_SUCCESS'  => './Public/tips/tips.html', // 默认成功跳转对应的模板文件
		'ERROR_PAGE'           => './Public/tips/error.html',// 异常和错误
		
		
		
		//多语言配置
		'LANG_SWITCH_ON'       => false,   // 开启多语言功能
		'LANG_AUTO_DETECT'     => false, // 自动侦测语言 开启多语言功能后有效
		'DEFAULT_LANG'         => 'zh-cn', // 默认语言
		'LANG_LIST'            => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
		'VAR_LANGUAGE'         => 'l', // 默认语言切换变量
		//默认发送返回参数的对照
		"site" => array("total","ls","zf","28","cc"),
		"ls"	 => array("m.liansuo.com","wap.liansuo.com","m.xqd8888.com","www.liansuo.com","m.longcangxuan.com","m.sdcf88.com","m.muouwangluo.com"),
		"zf"	=>array("wap.zhifuwang.cn","wap.sj99188.com", "wap.zft888.com", "wap.1522828.com", "wap.1562828.com", "wap.wapluntai-pifa.com","wap.chuxueit.net","wap.51xiaoxiao.net","wap.luntai-pifa.com", "wap.xyk371.cn", "wap.ahhsshy.cn", "wap.zhifuwang.net.cn","wap.yumidaba.cn", "wap.bjiso.net.cn", "wap.bjiso.net.cn", "wap.zhifuwang.org.cn", "3g.chuangye.com", "3g.1562828.com","wap.yangdaidai.com", "wap.hungfai.com.cn", "wap.1shan.cn", "wap.800bao.cn", "wap.51t6.cn", "wap.h-bb.cn", "wap.dllvyou.net","wap.huai-an.net","wap.chuangye.com"),
		"r_28_1" => "数据格式不对，传来的数据不是数组",
		"r_28_2" => "传入的客户ID、项目ID不符合基本要求",
		"r_28_3" => "必填信息项内容为空",
		"r_28_4" => " 留言接口已经关闭，该站留言不接收 ",
		"r_28_5" => "同一天对项目已留过言 ",
		"r_28_6" => "留言发送接口权限有问题",
		"r_28_7" => "过期留言",
		"r_28_8" => "项目已经停止投放",
		"r_28_9" => "用户跳转自己网站，我们不需要留言 ",
		"r_28_15" => "当前项目超标 则不接收 ",
		"r_28_21" => "单人留言条数1日内不大于3条",
		"r_28_22" => "单人留言条数10日内不大于10条",
		"r_28_38" => "判断超标与否，超标则不接收",
		
		
);
return array_merge($config,$array);