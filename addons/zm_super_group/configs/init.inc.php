<?php
global $_W;
//初始化模块项目
//网站根目录
define('LOUIE_WQ_ROOT',substr(dirname(__FILE__),0,-25));

//微擎ibrary目录
define('LOUIE_WQ_IBRARY',LOUIE_WQ_ROOT.'framework/library/');

//模块根路径
define('LOUIE_MODULE',MODULE_URL);

//模块跟目录
define('LOUIE_MODULE_DIR',MODULE_ROOT);

//模块主题路径
define('LOUIE_THEME',LOUIE_MODULE.'themes/');

//模块css路径
define('LOUIE_CSS',LOUIE_MODULE.'themes/style/');

//模块JS路径
define('LOUIE_JS',LOUIE_MODULE.'themes/js/');

//IM模块JS路径 
define('LOUIE_IM_JS',LOUIE_THEME.'imjs/'); 

//IM模块表情包路径
define('LOUIE_FACE',LOUIE_MODULE.'themes/face/'); 

//模块图片路径
define('LOUIE_IMG',LOUIE_MODULE.'themes/images/');


//模块字体路径
define('LOUIE_FONT',LOUIE_MODULE.'themes/fonts/');

//模块字体路径
define('LOUIE_FONT_DIR',LOUIE_MODULE_DIR.'/themes/fonts/'); 

//控制器路径
define('LOUIE_CONTROLLER',MODULE_ROOT.'/controller/');

//公共类路径
define('LOUIE_PUBLIC',MODULE_ROOT.'/public/');

//数据表前缀
define('LOUIE_PREFIX','zm_super_group_'); 

//当前模块标识
define('LOUIE_MODULE_NAME','zm_super_group');



