<?php 

class Collection extends BaseModel {
	var $c_name = '';
	var $ser_id = '';
	var $c_description;
	
	function Collection () {
		$this->BaseModel();
		$this->table = TP."_collections";
	}
	
	
	public function getByOwner($owner) {
		if(!$owner) return false;
		return $this->db->getArray("select * from ".$this->table." where user_id = $owner order by c_name");
	}
	
	/**
	 * finds a Collection by Id
	 * @see app/library/Basemodel#get()
	 */
	public function get($id) {
		
		
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

