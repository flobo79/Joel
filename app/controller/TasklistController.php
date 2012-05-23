<?php

/**
* Provides functions to create, edit and delete tasks.
*  
*  WARNING!!! DESIGN FAULT - SHOULD BE PART OF PROJECTS
*  
* @package joel
*/
class TasklistController extends BaseController {
	var $userid;
	var $projectID;
	var $excludeuserid;

	function Tasklist() {
		$this->db = $_SESSION['db'];
	}
	
	function getList($obj) {
		$sel_user = "t.user_id != ''";
		
		if(!intval($obj['project'])) die('no id given');
		
		
		if($_SESSION['joel']->user->user_id) {
			//$sel_user = " and u.user_id = ".$_SESSION['joel']->user->user_id;
		} elseif (isset($obj->excludeuserid)) {
			$sel_user = "t.user_id != ".$obj->excludeuserid;
		} else {
			$sel_user = "t.user_id != ''";
		}
		
		$query = "SELECT t.*, (t.t_currest - t.t_elapsed) as t_remain FROM 
					".TP."_tasks as t 
				WHERE 
					$sel_user AND t.project_id=".$obj['project']."
				ORDER BY 
					t.t_rank";
		
		// left join ".TP."_user as u on t.user_id = u.user_id
		$result = $this->db->execute($query) or die("died of query [$query] : " . $this->db->ErrorMsg());
		
		
		$list = $result->getArray();
		//$list = $_SESSION['joel']->plugins_apply('_tasklist', $list);
		
		return $list; 
	}
	
	function getTasklist($obj) {
		$list = $this->getList($obj);
		$json = new Services_JSON();
		echo $json->encode($list);
		return;
	}
	
	function updateOrder($obj) {
		if(isset($obj->order)) {
			$packet = explode(',',$obj->order);
			$set = array();
			foreach($packet as $k => $v) {
				$set[] = array($k+1, intval($v));
			}
			
			$result = $this->db->execute("update `".TP."_tasks` set `t_rank` = '?' where `task_id` = '?' LIMIT 1", $set)
			 or die("died of query [$query] : " .$this->db->ErrorMsg());
		}
	}
	
	function copyTasks($obj) {
		
		$tasks = array();
	  	$pid = $_SESSION['joel']->project->project_id;
		
		// at first load all tasks that going to be copied
		foreach($obj->tasks as $id) {
			$q = $this->db->Execute("select * from ".TP."_tasks where task_id = $id LIMIT 1");
			$result = $q->getArray();
			$tasks[] = $result[0];
		}
		
		// where to add these tasks in the tasklist
		if(intval($obj->insertafterid)) {
			// task to insert new tasks after
			$previoustask = $this->db->getArray("select task_id, t_rank, project_id from ".TP."_tasks where task_id = ".intval($obj->insertafterid)." LIMIT 1");
			$previoustask = $previoustask[0];
			
			// update rank for all following rows of this project
			$this->db->execute("update ".TP."_tasks set t_rank = t_rank+".count($obj->tasks)." where t_rank >= $previoustask[t_rank] and project_id = $pid");
		
		} else {
			// inserting tasks at the end of the tasklist
			$sql_getlasttask = "SELECT MAX(t_rank) AS `t_rank` FROM `".TP."_tasks` WHERE `project_id` = '$pid' LIMIT 1";
			$previoustask = $this->db->getArray($sql_getlasttask);
			$previoustask = $previoustask[0];
		}
		
		// set the order position
		$rank = $previoustask['t_rank'];
		
		
		foreach($tasks as $k => $task) {
			// remember id of original task
			$origtask_id = $task['task_id'];
			
			// preparing data for new task
			$task['project_id'] = $pid;
			$task['t_rank'] = $rank;
			unset($task['task_id']);
			
			// create insert query and execute it 
			$thisq = "insert into ".TP."_tasks set ";
			foreach($task as $f => $v) { $thisq .= "$f = '$v', "; }
			$thisq = substr($thisq,0,-2);
			$this->db->execute($thisq);
			
			// adding new created task id to task
			$task['task_id'] = $this->db->insert_id();
			$task['t_remain'] = $task['t_currest'] - $task['t_elapsed'];
			
			// writing back this new task to tasks array
			$tasks[$k] = $task;
			$rank++;
		}
		
		return $tasks;
	}
	
	
	function deleteTasks ($obj) {
		foreach($obj->delids as $id) {
			$task = new Task($id);
			$task->delete();
		}
	 	$this->db->execute("OPTIMIZE TABLE `".TP."_tasks`");
	}
  
	/**
	 * converts seconds into hours
	 * 
	 * @return 
	 * @param $s Object
	 */
	function s2h($s) {
		return sprintf("%d:%02d",floor($s/3600), ($s % 3600) / 60);
	}
	
	function drawPrio($prio, $task) {
		$prioHTML = '<div id="0_'.$task['task_id'].'" onclick="setPrio(this)"></div>';
		for($i=1;$i<5;$i++) {
			$prioHTML .= '<div class="'.($i<=$task['t_prio']?'star':'dot').'" id="'.$i.'_'.$task['task_id'].'" onclick="setPrio(this);" ></div>';
		}
		return $prioHTML;
	}
}