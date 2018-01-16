<?php

defined('IN_IA') or exit('Access Denied');

class zm_super_groupModuleProcessor extends WeModuleProcessor {
	public function respond(){ 
	    global $_W;
	    include dirname(__FILE__).'/configs/run.inc.php';  
	    $_content = $this->message['content']; 
	    $_invite = new InviteAction();  
        return $this->respNews($_invite->responseGroup($_content));   
	    }
}