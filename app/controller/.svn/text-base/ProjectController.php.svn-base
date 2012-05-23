<?php

/**
* Represents the Projects in Timemachine and provides functions to create, modify and delete projects.
*
* @package Project 
*/

class ProjectController extends BaseController {
	/**
	* 
	* @return 
	*/
	var $project_id;
	var $user_id;
	var $p_name;
	var $p_description;
	var $p_collection;
	
	function ProjectController($id=false) {
		$this->Basecontroller();
		$this->table = TP.'_projects';
		
		if($id && intval($id)) {
			$this->load($id);
		}
	}
	
	function get($obj) {
		if(!isset($obj['id'])) die("no project id given");
		$id = $obj['id'];
		$row = $this->db->getArray("select project_id from `".$this->table."` where project_id='$id' limit 2");
		if(isset($row[0])) {
			return $row;
		} else {
			die("project ID not found");
		}
	}
	
	
	function getAll($obj) {
		return $this->db->getArray("select * from `".$this->table."` where user_id='".$this->joel->user->user_id."'");
	}
		
	
	/**
	* Creates a new project and returns the newly created project id. 
	* Additionally it displays the new project id for ajax purposes
	*
	* @param $obj Object containing parameter 'title' wich is the Title of the Project
	* @return new projects ID
	*/
	function create($obj) {
		$response = array();
	  	$title = $obj->title;
		$collection = $obj->collection;
		
		if(!intval($collection)) {
			$response['result'] = "failure";
			$response['reason'] = "collection id provided is not valid";
		}
		
		/** TODO: Check if collection ID belongs to user */ 
	  	if(strlen($title) > 0) {	
			$settitle = $_SESSION['joel']->castString($title);
			
			$query = "INSERT INTO `".$this->table."` 
				set 
					p_name = '".mysql_escape_string($title)."', 
					collection_id = '".intval($collection)."', 
					user_id='".$_SESSION['joel']->user->user_id."'";
					
			$result = $this->db->execute($query);
		    if($this->db->errormsg()) {
		    	$response['result'] = "failure";
				$response['reason'] = $this->db->errormsg();
				return $response;
		    }
		    
			$id = $this->db->Insert_ID();
			
			// create a couple of empty rows
			$obj = new emptyClass();
			$obj->projectID = $id;
			
			// load new project as current project
			$this->load($id);
			
			// create 10 project tasks
			$tasks = new Task();
			for($i=0;$i<10;$i++) {
				$tasks->create();
			}
			
			// send new project to client
			$response['result'] = "success";
			$response['project'] = array(
				'project_id' => $id,
				'p_name' => $settitle,
				'origest' => 0,
				'elapsed' => 0,
				'currest' => 0,
				'remain' => 0
			);
			
	  	} else {
			$response['result'] = "failure";
			$response['reason'] = "please enter a project name.";
		}
		
		return $response;
	}

	/**
	 * 
	 * 
	 * @param $data
	 * @return unknown_type
	 */
	function update($data) {
		if(is_array($data) || is_object($data)) {
			$set = array();
			
			foreach($data as $k => $v) {
				if(property_exists(get_class($this), $k) && $k != 'project_id') {
					$this->$k = $v;
					$set[] = "`$k` = '".mysql_escape_string($v)."'";
				}
			}
			
			$_SESSION['db']->execute("update `".$this->table."` set ".implode(", ",$set)." where project_id = '".$this->project_id."' LIMIT 1")
 			or die($this->db->ErrorMsg());
		}
	}
	
	
	
	/**
	* deletes a project and all related tasks
	*
	* @param $obj Object containing variable 'id' wich represents the project id
	*/
	function delete() {
	  $_SESSION['joel'] -> plugins_apply('_project_delete', $id);
	  
	  $this->db->execute("DELETE from `".TP."_tasks` WHERE `project_id` = '".$id."'");
	  
	  $tasks = $this->db->fetchArray("select id from ".TP." where project_id = '".$this->project_id."'");
	  $this->db->execute("DELETE from `".$this->table."` WHERE `project_id` = '".$this->project_id."'");
	}
	
	
	function getByCollectionId($obj) {
		if(($obj["id"])) {
			
			$id = $obj["id"];
			return $this->db->getArray("select * from ".$this->table." where collection_id = $id");
		}
		return false;		
	}
}


?>