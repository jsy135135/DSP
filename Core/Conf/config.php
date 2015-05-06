<?php

/**
 * @name $config 系统配置文件
 */
$config = require './config.php';
$array = array(
    'APP_GROUP_LIST' => 'Admin,Home', // 分组
    'TMPL_FILE_DEPR' => '_', // 模板文件MODULE_NAME与ACTION_NAME之间的分割符，只对项目分组部署有效
    'DEFAULT_GROUP' => 'Home', // 默认分组
    'URL_MODEL' => 3, // URL兼容模式
    'URL_CASE_INSENSITIVE' => true, // URL是否不区分大小写 默认区分大小写
    'DB_FIELDTYPE_CHECK' => true, // 是否进行字段类型检查
    'DATA_CACHE_SUBDIR' => true, // 哈希子目录动态缓存的方式
    'DATA_PATH_LEVEL' => 2,
    'TMPL_STRIP_SPACE' => false, //是否去除模板文件里面的html空格与换行
    'TOKEN_ON' => true, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => true, //令牌验证出错后是否重置令牌 默认为true
    'TMPL_ACTION_ERROR' => './Public/tips/tips.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => './Public/tips/tips.html', // 默认成功跳转对应的模板文件
    'ERROR_PAGE' => './Public/tips/error.html', // 异常和错误
    //多语言配置
    'LANG_SWITCH_ON' => false, // 开启多语言功能
    'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'LANG_LIST' => 'zh-cn,en-us', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE' => 'l', // 默认语言切换变量
    //默认发送返回参数的对照
    "site" => array("total", "ls", "zf", "28", "cc"),
    "ls" => array("m.liansuo.com", "wap.liansuo.com", "m.liansuo.net", "m.xqd8888.com", "www.liansuo.com", "m.longcangxuan.com", "m.sdcf88.com", "m.muouwangluo.com", "m.51s287.com", "m.51s51.com", "m.51s53.com", "m.52s287.com", "m.523liansuo.com"),
    "zf" => array("wap.zhifuwang.cn", "wap.sj99188.com", "wap.zft888.com", "wap.1522828.com", "wap.1562828.com", "wap.wapluntai-pifa.com", "wap.chuxueit.net", "wap.51xiaoxiao.net", "wap.luntai-pifa.com", "wap.xyk371.cn", "wap.ahhsshy.cn", "wap.zhifuwang.net.cn", "wap.yumidaba.cn", "wap.bjiso.net.cn", "wap.bjiso.net.cn", "wap.zhifuwang.org.cn", "3g.chuangye.com", "3g.1562828.com", "wap.yangdaidai.com", "wap.hungfai.com.cn", "wap.1shan.cn", "wap.800bao.cn", "wap.51t6.cn", "wap.h-bb.cn", "wap.dllvyou.net", "wap.huai-an.net", "wap.chuangye.com", "uc.zhifuwang.cn", "ucwap.biz178.com","wap.98t.cn", "wap.dz888.net", "wap.jsy135.com", "wap.wzzj8.cn", "wap360.biz178.com"),
    "WP" => array("3w.1552828.com", "3w.1882828.com", "3w.wp28.com", "sogou.28u2.com", "uc.wp28.com", "sogou.28u2.com"),
    #一个账户对应一个域名
    #   账户  域名
// 百度  bj-1342828  200.1342828.com
//     xqd88   kb.xqd8888.com
//     16328   200.28u0.com
//     AP-04   800.xueliong.com
//     DY-02   800.quikio.cn
// 360 ka52s288    360.52s288.com
//     ka91jmw 360.91jmw.com
//     ka28g9  360.28g9.com
//     ka51s51 360.51s51.com
//     创业家园2   360.xqd8888.com
// 搜狗  51s588@sogou.com    200.51s58.com
//     52s285@sogou.com    200.52s285.com
//     52s289@sogou.com    200.52s289.com
//     51s2821@sogou.com   sogou.51s282.com
//     52s2801@sogou.com   sogou.52s280.com
//     51s2891@sogou.com   sogou.51s289.com
//     51s521@sogou.com    sogou.51s52.com
//     52s2861@sogou.com   sogou.52s286.com
//zk.28s0.com   sm.51s283.com  zk.hbmpu.com 为sooe现在投放域名
    "jm" => array("zk.hbmpu.com", "71593.wap.sooe.cn", "200.1342828.com", "kb.xqd8888.com", "200.28u0.com", "800.dianka999.com", "sm.51s283.com", "800.chun-ge.com", "800.xueliong.com", "800.it-sanda.com", "800.quikio.cn", "360.52s288.com", "360.91jmw.com", "360.28g9.com", "360.51s51.com", "360.xqd8888.com", "200.51s58.com", "200.52s285.com", "200.52s289.com", "sogou.51s282.com","sogou.52s280.com", "sogou.51s289.com", "sogou.51s52.com", "sogou.52s286.com", "sm.51s285.com", "zk.28s0.com", "sm.51s283.com", "200.28cyz.com", "200.28liansuo.com", "200.91zfb.com", "200.duang168.com", "200.gh28gh.com", "200.jjskw.cn", "200.jmw91.com", "200.jsk321.com", "200.k1001.cn", "200.lost8.cn", "200.wangp91.com", "800.08vw.com", "800.8580808.com", "sm.52s288.com", "200.hehe13.com", "200.ly0009.com", "sogou.bishi.net.cn", "sogou.szpingnet.com.cn", "sogou.webmay.com.cn", "200.3dyz.net", "200.16cool.com", "200.9191jm.com", "200.chuangy168.com", "200.jhb91.com", "200.jmw28.com", "200.jmz188.com", "200.16dgg.cn", "shangkeyou.315xyz.com", "sogou.cnlongdingtea.com", "sogou.shanhuwan.com.cn", "200.52lians.com", "200.98liansuo.com", "200.chye188.com", "200.cyz91.com",),
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
return array_merge($config, $array);
