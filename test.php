<?php
/** 
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
// 开启日志
//ini_set("display_errors", "On");
//error_reporting(E_ALL);

// var_dump('TODO jieqiangtest 111');exit;
require './framework/bootstrap.inc.php';
//core();
//var_dump('TODO jieqiangtest 111222',$host,$bindhost);exit;
$host = $_SERVER['HTTP_HOST'];

$bindhost = pdo_fetch("SELECT * FROM ".tablename('site_multi')." WHERE bindhostss = :bindhost", array(':bindhost' => $host));

//WeUtility::logging('TODO debug2 runtime=' . $runtime . 's', array('file' => 'fetch() ', 'sql2' => $sql2, '$params' => $params));

var_dump('TODO jieqiangtest 111222',$host,$bindhost);exit;
if (!empty($host)) {
	$bindhost = pdo_fetch("SELECT *s FROM ".tablename('site_multi')." WHERE bindhost = :bindhost", array(':bindhost' => $host));
	if (!empty($bindhost)) {
		header("Location: ". $_W['siteroot'] . 'app/index.php?i='.$bindhost['uniacid'].'&t='.$bindhost['id']);
		exit;
	}
}
if($_W['os'] == 'mobile' && (!empty($_GPC['i']) || !empty($_SERVER['QUERY_STRING']))) {
	header('Location: ./app/index.php?' . $_SERVER['QUERY_STRING']);
} else {
	header('Location: ./web/index.php?' . $_SERVER['QUERY_STRING']);
}
