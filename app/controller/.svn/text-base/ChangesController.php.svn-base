<?php

/**
 * Stores all changes that are made to Tasks
 * 
 */
class ChangesController {
	
	function Changes() {
		$this->table = TP.'_tasks_changes';
		$this->db = $_SESSION['db'];
	}
	
	
	/**
	 * gets all changes of a task
	 * @return 
	 */
	function get($id) {
		if(!intval($id)) return false;
		return $this->db->fetchArray('select from '.$this->table.' where tc_task_id = '.intval($id).' order by tc_date');
	}
	
	/**
	 * inserts a new task change
	 * 
	 * @return 
	 * @param object $array
	 */
	function insert($fieldname, $value, $task_id) {
		if(!isset($fieldname)) return false;
		if(!isset($value)) return false;
		if(!isset($task_id)) return false;
		
		$this->db->execute("insert into ".$this->table." set 
			`tc_fieldname` = ".$this->db->QMagic($fieldname).", 
			`tc_value` = ".$this->db->QMagic($value).",
			`tc_task_id` = ".intval($task_id).", 
			`tc_user_id` = ".$_SESSION['joel']->user->user_id.",
			`tc_date`=NOW()");
		
		return $this->db->insert_ID();
	}
	
	
	/**
	 * deletes all changes for a given set of task ids
	 * @return 
	 * @param object $obj
	 */
	function delete($obj) {
		if(is_iterable($obj)) {
			foreach($obj as $o) {
				if(intval($o)) $this->db->execute("delete from ".$this->table." where `tc_task_id` = $o");
			}
		} elseif(intval($obj)) {
			$this->db->execute("delete from ".$this->table." where `tc_task_id` = $obj");
		}
	}
}


?>