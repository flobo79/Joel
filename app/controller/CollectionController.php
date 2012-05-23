<?php
/** 
 * Class collection
 * 
 */
class CollectionController extends BaseController {
	var $collection_id = false;
	var $c_name = '';
	var $c_info = '';
	var $c_user = false;
	var $db_fields = array();
	
	
	function CollectionController ($obj=false) {
		$this->BaseController();
		$this->table = TP.'_collections';
	}
	
	
	/**
	 * Returns a List of all Collections of a user
	 * 
	 * @return unknown_type
	 */
	public function getAll() {
		$collections = new Collection();
		
		return $collections->getByOwner($this->joel->user->user_id);
	}
	
	
  	/**
  	 * Updates a Collection
  	 * 
	 * expects the fields to be updated as an array:
	 * $obj[fields] = array("u_name" => "Charles Bronson"[,...]);
	 */
	function update ($obj) {
		
	}
	
	
	/**
	 * Creates a new Collection
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	function create($obj) {
		$error = array();
		$info = "";
		$name = "";
		
		if($obj->name == '') $error[] = "name";
		
		
		// check if collection exists
		if(count($error) == 0) {
			$info = "";
			$name = mysql_real_escape_string($obj->name);
			if(isset($obj['info'])) $info = mysql_real_escape_string($obj['info']);
			
			$collections = new Collection();
			$existing = $collections->find("c_name = '$name'");
			if($existing[0]) $error[] = "name exists";
		}
		
		
		if(count($error) == 0) {
			$newcollection = $collections->insert(array(
				'c_name' => $name,
				'c_info' => $info,
				'user_id' => $_SESSION['joel']->user->user_id
			));

			
			if($newcollection) {				
				$result = array();
				$result['collection'] = $newcollection;
				$result['result'] = 'success';
			}
		} else {
			$result = array('error' => $error, 'result' => 'failure');
		}
		
		return $result;
	}
	
	/**
	 * loods the details of a collection
	 * 
	 * @param $obj
	 * @return unknown_type
	 */
	function get($obj) {
		
		if(!intval($obj['id'])) die("no id given");
		
		echo $query="select * from `".$this->table."` where collection_id = `$obj[id]` LIMIT 1";
		$data = $_SESSION['db']->getArray($query);
		
		print_r($data);
		//return $_SESSION['joel']->plugins_apply('_collection_getData',$return);
		return $data;
	}
	
	
	/**
	 * Deletes a Collection and its related projects 
	 * 
	 * @param $id
	 * @return unknown_type
	 */
	function delete($id) {
		if(!intval($id)) {
			die("no id given");
		} else {
			$projects = new Project();
			$projectslist = $projects->getBycollectionId($id);
			if($projectslist) {
				$project = $projects($id);
				if($project) {
					$project->delete();
				}
			}
			return true;
		}
	}
}