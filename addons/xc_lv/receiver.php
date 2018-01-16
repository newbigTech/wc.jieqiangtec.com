<?php
/**
 * 微旅游模块订阅器
 *
 */
defined('IN_IA') or exit('Access Denied');
class Xc_lvModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}