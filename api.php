<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited  for more details.
 */
error_reporting(ALL);
define('IN_API', true);
require_once './framework/bootstrap.inc.php';
require_once './api/functions.php';

$c  = $controller = $_GPC['c'] ?: 'index';
$a  = $action = $_GPC['a'] ?: 'index';
$do = $_GPC['do'] ?: 'index';

//$sql  = "SELECT * FROM `ims_ewei_shop_goods` LIMIT 0, 1000";
//$list = pdo_fetchall($sql);
//var_dump('IA_ROOT==',IA_ROOT,'$list==', $list);

$file = IA_ROOT . '/api/' . $c . '.ctrl.php';
require $file;