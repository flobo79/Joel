<?php

/**
 * plugin Taskdetails
 * 
 * @return
 * @package plugins
 */
class plugin_taskdetailsController extends Plugin {
	var $filesdir;
	//var $permission = 1;
	
	function plugin_taskdetailsController () {
		$this->filesdir = dirname(__FILE__)."/files";
	}
	
	function _tasklist($tasklist) {
		if(is_array($tasklist)) {
			foreach($tasklist as $k => $entry) {
				$tasklist[$k]['files'] = $this->loadFileslist($entry['task_id']); 
			}
		}
		return $tasklist;
	}
	
	function loadFileslist($task_id) {
		$return = array();
		$dir = $this->filesdir."/".$task_id;
		if (is_dir($dir) && $dh = opendir($dir)) {
			$i=0;
	        for (;false !== ($file = readdir($dh));) {
	        	if($file != "." && $file != "..") {
	        		$return[$i]['name'] = $file;
					$return[$i]['ext'] = strtolower(substr($file, strrpos($file, '.') +1));
					$i++;
				}
			}
		}
		return $return;
	}
	
	function delFile($obj) {
		if(intval($obj->taskID) && file_exists($file = $this->filesdir."/".$obj->taskID."/".urldecode($obj->filename))) {
			unlink($file);
		}
	}
	
	function getFiles($obj) {
		$files = $this->loadFileslist($obj->task_id);
		$_SESSION['joel']->jsonResponse($files);
	}
	
	function loaddetails($obj) {
		global $timemachine;
		
		// load task details from details table and filesystem
		echo $timemachine->table_exists('foobar');
		
		//echo $obj->id;
	}
	
	function _task_create($obj) {
		$obj->files = array();
		return $obj;
	}
	
	function _deleteTask ($id) {
		if(intval($id)) {
			$path = $this->filesdir."/".$id."/";
			if(is_dir($path) && $handle = opendir($path)) {
				for (;false !== ($file = readdir($handle));) if($file != "." && $file != "..") unlink($path.$file);
				closedir($handle);
				rmdir($path);
			}
		}
	}
	
	
	function _tasklist_copyTasks($tasks) {
		$orig = $tasks[0];
		$new = $tasks[1];
		
		foreach($orig as $k => $to) {
			$fromdir = $this->filesdir."/".$to;
			
			if (is_dir($fromdir) && $dh = opendir($fromdir)) {
				$todir = $this->filesdir."/".$new[$k]['task_id'];
				mkdir($todir);
				
		        for (;false !== ($file = readdir($dh));) if($file != "." && $file != "..") copy($fromdir."/".$entry,$todir."/".$entry);
			}
			
			$new[$k]['files'] = $this->loadFileslist($new[$k]['task_id']);
		}
		
		return $new;
	}
}

?>
