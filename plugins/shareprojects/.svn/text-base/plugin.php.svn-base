<?php

/**
 * Provides the functionalities to share projects with other 
 * user.
 * 
 * @package Plugin_ShareProjects
 * @author Florian Bosselmann
 * 
 */

class plugin_shareprojectsController extends Plugin {
	var $addProject = false;
	var $cb_key = false;
	//var $permission = 1;
	
	function plugin_shareprojectsController() {
		/*
		$this->table = TP.'_plugin_shareprojects';
		$_SESSION['db']->execute("CREATE TABLE IF NOT EXISTS `".$this->table."` (
			`user_id` int(11) NOT NULL,
		  `projects` varchar(100) collate utf8_bin default NULL,
		  PRIMARY KEY  (`user_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		
		*/
	}
	
	
	function _getcollectionlist($list) {
		$shared = $_SESSION['db']->getArray("select * from ".$this->table." where user_id = '".$_SESSION['joel']->user->user_id."' LIMIT 1");
		$shared = explode(",",$shared[0]['projects']);
		
		if(is_array($shared) && count($shared)) {
			$list[]	= array('collection_id' => 999, 'c_name' => 'Shared Projects', 'c_info' => '', 'c_user_id' => $_SESSION['joel']->user->user_id);
		}
		return $list;
	}
	
	/**
	 * modifier for the getProjects method to add additional
	 * shared projects.
	 * 
	 * @return $projects Object modified projectslist
	 * @param $projects Object projectslist
	 */
	function _getProjects($projects) {
	
		$shared = $_SESSION['db']->getArray("select * from ".$this->table." where user_id = '".$_SESSION['joel']->user->user_id."' LIMIT 1");
		$shared = explode(",",$shared[0]['projects']);
		
		if(is_array($shared)) {
			
			$where = '';
			foreach($shared as $id) {
				if($id) $where .= ' or p.project_id = '.$id.'';
			}
			
			$sql = "select 
					SUM(t.t_currest) as currest, 
					SUM(t.t_origest) as origest, 
					SUM(t.t_elapsed) as elapsed, 
					SUM(t.t_currest) - SUM(t.t_elapsed) as remain, 
					p.p_name, p.project_id,
					'0' as bookmarked,
					'1' as shared,
					'999' as collection_id
				from 
					".TP."_projects as p 
					left join ".TP."_tasks as t 
					on t.project_id = p.project_id 
				where p.project_id = 'test' ".$where."
				group by 
					p.project_id 
				order by 
					p.p_name";
			
			$list = $_SESSION['db']->getArray($sql);
			
			if(is_array($list)) {
				$u_bookmarks = explode(",",$_SESSION['joel']->user->u_bookmarks);
				
				foreach($list as $k => $l) {
					if(in_array($l['project_id'],$u_bookmarks)) $l['bookmarked'] = 1;
					$projects[] = $l;
				}
			}
		}
		return $projects;
	}
	
	
	/**
	 * modifier for users getData method
	 * adds a flag to the object if a addProject request is present
	 * 
	 * @return 
	 * @param $obj Object
	 */
	function _user_getData($obj) {
		if($this->addProject) {
			$obj['addProject'] = $this->addProject;
		}
		return $obj;
	}
	
	function decline() {
		$_SESSION['db']->execute("delete from `".TP."_clipboard` where `cb_key`='".mysql_real_escape_string($this->cb_key)."'");
		$this->addProject = false;
	}
	
	function accept() {
		if(isset($this->addProject)) {
			$current = $_SESSION['db']->getArray("select * from `".$this->table."` where `user_id`='".$_SESSION['joel']->user->user_id."'");
			if(is_array($current) && isset($current[0])) {
				$sql = "update `".$this->table."` set `projects` = '".(str_replace(','.$this->addProject['project_id'],'',$current[0]['projects']).','.$this->addProject['project_id'])."' where `user_id` = '".$_SESSION['joel']->user->user_id."' LIMIT 1";
			} else {
				$sql = "insert into `".$this->table."` set `projects` = ',".($this->addProject['project_id'])."', `user_id` = '".$_SESSION['joel']->user->user_id."'";
			}
			
			$_SESSION['db']->execute($sql);
			$_SESSION['db']->execute("delete from `".TP."_clipboard` where `cb_key`='".mysql_real_escape_string($this->cb_key)."'");
		
			$this->addProject = false;
		} else {
			echo "request invalid";
		}
	}
	
	/** 
	 * deletes a project from a users shared projects list
	 * 
	 * @return nothing
	 * @param $obj Object containing project_id
	 */
	function delProject($obj) {
		if(!intval($obj->project_id)) die("invalid request data");
		$sql = "update `".$this->table."` set `projects` = REPLACE(projects, ',".$obj->project_id."', '') ";
		$sql2 = "update `".TP."_user` set `u_bookmarks` = REPLACE(u_bookmarks, ',".$obj->project_id."', '') ";
		
		$_SESSION['db']->execute($sql);
		$_SESSION['db']->execute($sql2);
		
		if($_SESSION['joel']->user->u_working_project == $obj->project_id) {
			$_SESSION['joel']->Stopwatch_stop();
		}
	}
	
	
	/**
	 * send request email and write request to clipboard
	 * 
	 * @return nothing
	 * @param $obj Object
	 */
	function sendInvitation($obj) {
		$k = substr(md5(microtime()),0,10);
		$cb_content = array(
			$obj->email,						// to email address
			$_SESSION['joel']->user->user_id		// from whom
		);
		
		$_SESSION['db']->execute("insert into `".TP."_clipboard` set `cb_key`='$k', `cb_content` = '".mysql_real_escape_string(implode("::",$cb_content))."'");
		$email = "Hi there,\n\n".$_SESSION['joel']->user->u_name." has invited you to share a Joel project. If you agree, please click this link to accept this invitation:\n
http://".$_SERVER['HTTP_HOST'].PATH."/?share=".$k;
		
		mail(trim($obj->email), 'Share Project', $email, 'FROM:Joel');
	}
}
	
?>
