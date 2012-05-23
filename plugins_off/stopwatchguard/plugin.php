<?php

/**
*
*
*
*/
class plugin_stopwatchguard extends Plugin {
	function plugin_stopwatchguard() {
		$this->table = TP.'_plugin_stopwatchguard';
		$_SESSION['db']->execute("CREATE TABLE IF NOT EXISTS `".$this->table."` (
		`task_id` int(11) NOT NULL,
		  PRIMARY KEY  (`task_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
	}
	
	function _get($obj) {
		// look for active timers older than 8 hours
		$sql = "select u.user_id,u.u_name,u.u_email, u.u_working_since, u.u_working_on, t.t_description, p.p_name from 
			`".TP."_user` as u 
			right join `".TP."_tasks` as t on u.u_working_on = t.task_id 
			right join `".TP."_projects` as p on t.project_id = p.project_id 
			
			 where u.u_working_since > '".(8*3600)."' group by u.user_id ";
			 
		$list = $_SESSION['db']->getArray($sql);
			 
		if(count($list)) {
			foreach($list as $entry) {
				$check = $_SESSION['db']->getArray("select * from ".$this->table." where task_id = '".$entry['u_working_on']."' LIMIT 1");
				if(!isset($check[0]) && $entry['u_email']) {
					$_SESSION['db']->execute("insert into ".$this->table." set task_id = '".$entry['u_working_on']."'");
					$message = "Hi ".$entry['u_name'].",
					
this is an automatic reminder message to tell you that your task '".$entry['t_description']."' in project '".$entry['p_name']."' is now running for over 8 hours. 

Thanks - your timemachine";
					
					
					
					
					mail($entry['u_email']," running timer notice",$message,"from:Timemachine <no-return@timemachine>");
				}
			}
		}
	}
}


?>