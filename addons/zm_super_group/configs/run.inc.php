<?php
//开发者模式   (模块开发完成后将参数改为0，屏蔽一切报错)
//error_reporting(E_ALL);

//运行处理
//接收微擎全局变量
global $_W,$_GPC;
//引入初始化文件
include dirname(__FILE__).'/init.inc.php';
include LOUIE_MODULE_DIR.'/sdk/TimRestInterface.php';
include LOUIE_MODULE_DIR.'/sdk/TimRestApi.php';

// //引入PHP导入类
// include LOUIE_MODULE_DIR.'/phpexcel/PHPExcel.php';
// include LOUIE_MODULE_DIR.'/phpexcel/PHPExcel/IOFactory.php';
// include LOUIE_MODULE_DIR.'/phpexcel/PHPExcel/Reader/Excel2007.php';
 



//自动引入类
spl_autoload_register(function($_className){
    if(substr($_className,-6) == 'Action'){
        include LOUIE_CONTROLLER.$_className.'.class.php';
    }else{
        include LOUIE_PUBLIC.$_className.'.class.php';
    }
});

