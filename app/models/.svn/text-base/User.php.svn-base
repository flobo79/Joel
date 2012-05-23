<?php 

class User extends Basemodel {
	var $user_id = false;
	var $u_name = '';
	var $u_bookmarks = '';
	var $u_email;
	var $u_login;
	var $u_password;
	var $u_type = '';
	var $u_locale = DEFAULT_LOCALE;
	var $u_working_on = false;
	var $u_working_since = false;
	var $u_working_description = '';
	var $u_working_project = '';
	var $u_skin = DEFAULTSKIN;
	var $db_fields = array();
	
	function User () {
		$this->Basemodel();
		$this->table = TP."_user";
	}
	
	public function insert ($data) {
		foreach($data as $k => $v) {
			if(property_exists(get_class($this), $k))
				$set[] = "`$k` = '".mysql_escape_string($v)."'";
		}
		
		$query = "insert into `".$this->table."` set ".implode($set,", ")."";
		$this->db->execute($query) or die ($this->db->ErrorMsg());
		
		
		return $this->get($this->db->insert_id());
	}
	
	public function get($id) {
		
		$result = $this->db->getArray("select * from ".$this->table." where user_id = $id LIMIT 1");
		return (object)$result[0];
	}
	
	public function getAll() {
		return $this->db->getArray("select * from ".$this->table." order by u_name");
	}
	
	
	public function update ($data, $id) {
		$set = array();
		
		// these parameters are not allowed to be changed
		unset($data['user_id'], $data['u_password'], $data['u_login']);

		foreach($data as $k => $v) {
			if(property_exists(get_class($this), $k))
				$set[] = "`$k` = '".mysql_escape_string($v)."'";
		}
		
		$query = "update `".$this->table."` set ".implode($set,", ")." where user_id = ".$id." LIMIT 1";
		$this->db->execute($query) or die ($this->db->ErrorMsg());
	}
	
	
	public function find($where) {
//		echo "select * from ".$this->table." where ".$where;
		return $this->db->getArray("select * from ".$this->table." where ".$where);
	}
}

