<?php

/**
 * plugin resources
 * 
 * purpose of this plugin is to manage resources over projects and time. This module shows a 
 * calendar and various lists to display 
 * 
 * 
 * 
 * @package plugins
 */
class Plugin_resourcesController extends Plugin {
	


	function Plugin_resourcesController () {
		$this->table = TP.'_plugin_resources';
		$this->table_projects = TP.'_plugin_resources_projects';
		
		$this->templates = dirname(__FILE__)."/templates";
		//$this->install();
	}
	
	
	function permission() {
		return $_SESSION['joel']->user->u_type == 'admin' ? true : false;
	}
	
	function install() {
		
		$_SESSION['db']->execute($sql = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
			`r_user_id` int(11) NOT NULL,
		  	`projects` varchar(100) collate utf8_bin default NULL,
		  	PRIMARY KEY  (`r_user_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		
		CREATE TABLE IF NOT EXISTS `".$this->table_projects."` (
			`r_project_id` int(11) NOT NULL,
			`r_p_startdate` int(11) NOT NULL,
			`r_p_enddate` int(11) NOT NULL,
			`r_p_color` VARCHAR(20) collate utf8_bin default NULL,
			PRIMARY KEY  (`r_project_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		");
	}
	
	
	
	function load($obj) {
		// load user
		//$users = new User();
		//$userlist = $users->getAll();
		
		include($this->templates."/window.php");
	}
	
	function loadView($obj) {
		
		$view = is_string($obj) ? $obj : $obj->view;
		echo $view;
		switch($view) {
			default:
				// default is project view the project view requires a list of projects meta data
				// as addition to the existing projects list in joel
				
				
				
				
				break;
			case "team":
				
				$users = new UserController();
				$team = $users->getAll(true);
				
				include($this->templates."/view_team.php");
				
				break;
			
		}
		
	}
}

?>
