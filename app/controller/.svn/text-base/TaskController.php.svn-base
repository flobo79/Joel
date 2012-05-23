<?php

/**
* Provides functions to create, edit and delete tasks.
* @package timemachine_task
*/

/**
* Provides functions to create, edit and delete tasks.
*/
class TaskController extends BaseController {
	var $task_id = false;
	var $user_id = false;
	var $project_id = false;
	var $t_feature = '';
	var $t_description = '';
	var $t_origest = 0;
	var $t_currest = 0;
	var $t_elapsed = 0;
	var $t_remain = 0;
	var $t_prio = 0;
	var $t_rank = 0;
	var $db_fields = array();
	
	/**
	 * constructor
	 * 
	 * @return 
	 * @param object $data[optional]
	 */
	function Task($data=false) {
		$this->user_id = $_SESSION['joel']->user->user_id;
		$this->project_id = $_SESSION['joel']->project->project_id;
		$this->table = TP.'_tasks';
		$this->db = $_SESSION['db'];
		$this->errormsg = '';
		
		
		if(is_object($data)) {
			$this->create($data);
			
		} elseif (intval($data)) {
			$query = "SELECT *, t_currest - t_elapsed as t_remain FROM `".$this->table."` WHERE task_id = $data LIMIT 1";
			$result = $this->db->getArray($query);
		
			if(!isset($result[0]['task_id'])) {
				$this->errormsg = $this->db->ErrorMsg();
				return false;
			}

			foreach($result[0] as $k => $v) {
				$this->$k = $v;
				$this->db_fields[] = $k;
			}
		}
	}
	
	/**
	 * Returns all Task Parameter
	 * 
	 * @return array
	 */
	function getTask() {
		$vars = get_class_vars(get_class($this));
		
		foreach($vars as $k => $v) {
			$vars[$k] = $this->$k;
		}
		unset($vars['table'], $vars['db_fields'], $vars['db']);
		
		return $vars;
	}
  	
	/**
	 * create a new Task and returns the new task content
	 * 
	 * @param {Object} Object containing optional insertafterid, can also include task data to be added while creating
	 */
	function create($parameter=0) {
		if(isset($parameter->insertafterid) && intval($parameter->insertafterid)) {
			$previoustask = $this->db->getArray("SELECT `task_id`, `t_rank` from `".$this->table."` where `task_id` = '".intval($parameter->insertafterid)."' and `project_id` = '".$this->project_id."' LIMIT 1");
			$this->db->execute("update `".$this->table."` set t_rank = t_rank+1 where t_rank > ".$previoustask[0]['t_rank']." and `project_id` = '".$this->project_id."'");
			$parameter->t_rank = $previoustask[0]['t_rank']+1;
			unset($parameter->insertafterid);
		
		} else {
			$sql_getlasttask = "SELECT MAX(t_rank) AS `t_rank` FROM `".$this->table."` WHERE `project_id` = '".$this->project_id."' LIMIT 1";
			$lasttask = $this->db->getArray($sql_getlasttask);
			$parameter->t_rank = $lasttask[0]['t_rank']+1;
		}
		
		$set = '';
		foreach($parameter as $k => $v) {
			$this->$k = $v;
			$set .= ", `".$_SESSION['joel']->castString($k)."` = '".$_SESSION['joel']->castString($v)."'";
		}
		
		$this->db->execute("INSERT INTO `".TP."_tasks` SET `project_id`='".$this->project_id."', `user_id`='".$this->user_id."'".$set);
		if($this->db->ErrorMsg()) { echo $this->db->ErrorMsg(); }
		
		$this->task_id = $this->db->Insert_ID();
		#return $this->getTask();
	}
	
	/**
	 * Updates a task 
	 * 
	 * @return 
	 * @param object $data
	 */
	function update($data) {
		if(isset($obj->task_id)) {
			$task = new Task($obj->task_id);
			$task->update($obj);
		}
		
		if(is_array($data) || is_object($data)) {
			$set = array();
			
			foreach($data as $k => $v) {
				if(property_exists(get_class($this), $k) && $k != 'task_id') {
					$this->$k = $v;
					$set[] = "`$k` = '".mysql_escape_string(htmlentities($v))."'";
				}
			}
			
			$sql = "update `".$this->table."` set ".implode(", ",$set)." where task_id = '".$this->task_id."' LIMIT 1";
			$this->db->execute($sql)
 			or die($this->db->ErrorMsg());
		}
	}
	
	
	/** 
	 * updates a task field 
	 * 
	 * @return 
	 * @param object $obj
	 */
	function updateField($obj) {
	 	global $user;
		global $project;
		
		$fieldname 	= mysql_escape_string($obj->field);
		$value 		= mysql_escape_string($obj->value);
		
		if(in_array($fieldname,$this->db_fields) && $this->$fieldname != $value) {
			$result = $this->db->execute("update `".$this->table."` set `$fieldname` = '$value' where task_id = ".$this->task_id." LIMIT 1")
				or die("died of query: $sql\n" .  $this->db->ErrorMsg());
		
			// store changes in changes table
			$changes = new Changes();
			$changes->insert($fieldname, $value, $this->task_id);
			
			#$_SESSION['joel']->plugins_apply('_task_updateField', array($fieldname, $value));
		}
	}
	
	function delete() {
		$changes = new Changes();
		$changes->delete($this->task_id);
		  
	    $query = "DELETE from `".$this->table."` WHERE `task_id`=".$this->task_id." LIMIT 1";
		$this->db->execute($query) or die("died of query [$query] : " .$this->db->ErrorMsg());
		$_SESSION['joel']->plugins_apply('_deleteTask', $this->task_id);
	}
}