<?php 

class Project extends BaseModel {
	var $p_name = '';
	var $p_description;
	var $collection_id;
	var $user_id = '';
	
	/** 
	 * constructro
	 * 
	 * @return void
	 */
	function Project ($id = false) {
		$this->BaseModel();
		$this->table = TP."_projects";
		
		if($id) {
			return $this->get($id);
		}
	}
	
	
	/**
	 * getAll
	 * 
	 * @param $owner
	 * @return unknown_type
	 */
	public function getAll($owner) {
		if(!$owner) return false;
		return $this->db->getArray("select * from ".$this->table." where user_id = $owner order by c_name");
	}
	
	
	/**
	 * getByCollection
	 * 
	 * @param $collection
	 * @return unknown_type
	 */
	public function getByCollection($collection) {
		if(!$owner) return false;
		return $this->db->getArray("select * from ".$this->table." where collection_id = $owner order by c_name");
	}
	
	
	/**
	 * Gets a Project by Id
	 * 
	 */
	public function get($id) {
		$result = $this->db->getArray("select * from ".$this->table." where project_id = ".(int)$id);
		return (object)$result[0];
	}
	
	/** 
	 * finds a collection by query string
	 * 
	 */
	public function find($where) {
		return $this->db->getArray("select * from ".$this->table." where ".$where);
	}
	
	/** 
	 * Creates a new Collection
	 * 
	 */
	public function create ($obj) {
		
		
	}
	
	/** 
	 * updates an existing collection
	 *
	 */
	public function update ($obj, $id) {
		if(is_iterable($obj)) {
			$set = array();
			foreach($obj as $k => $v) {
				if(property_exists(get_class($this),$k)) {
					$this->$k = $v;
					if(in_array($k,$this->db_fields)) {
						$set[] = "`".$k."` = '".mysql_escape_string($v)."'";
					}
				}
			}
			
			$query = "update `".$this->table."` set ".implode($set,", ")." where collection_id = ".$this->collection_id." LIMIT 1";
			$_SESSION['db']->execute($query) or die ($_SESSION['db']->ErrorMsg());
			
			return true;		
		} else {
			return false;
		}
	}
	
	/**
	 * deletes a collection
	 * 
	 */
	public function delete ($id) {
		
	}
}

