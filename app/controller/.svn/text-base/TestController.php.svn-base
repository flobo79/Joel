<?php
/** 
 * User Class
 * 
 * 
 */
class TestController extends Basecontroller {
	
	function TestController () {
		
	}
	
	
  	/**
	 * expects the fields to be updated as an object:
	 * $obj[fields] = object("u_name" => "Charles Bronson"[,...]);
	 */
	function update ($obj) {
		
		$user = $_SESSION['joel']->user;
		$user->update($obj);
		
		if(is_object($obj)) {
			
			return true;		
		} else {
			return false;
		}
	}
	
	function addBookmark($id) {
		$bms = explode(",", $this->u_bookmarks);
		$new = array();
		if(!in_array($id, $bms)) {
			foreach($bms as $b) { if($b != '') $new[] = $b; }
			$bms[] = $id;
			$data = new emptyClass();
			$data->bookmarks = implode(",",$bms);
			$this->update ($data);
		}
	}
	
	function deleteBookmark($id) {
		$arr = explode(",", $this->u_bookmarks);
		$k = array_search($id, $arr);
		array_splice($arr,$k,1);
		$data = new emptyClass();
		$data->bookmarks = implode(",",$bms);
		$this->update ($data);
	}
	
	function create($obj) {
		$error = array();
		if(mysql_escape_string($obj['name']) == '') $error[] = "name";
		if(mysql_escape_string($obj['email']) == '') $error[] = "email";
		if(mysql_escape_string($obj['login']) == '') $error[] = "login";
		$obj['password'] ? md5($obj['password'] . "Das weiss ich nicht") : $error[] = "password";
		  
		// check if user exists
		if(count($error) == 0) {
			$query = "INSERT INTO `".$this->table."` SET u_name='$name', u_login='$login', u_email='$email', u_password='$password', u_bookmarks=';'";
			$result = $_SESSION['db']->execute($query) or die ($_SESSION['db']->ErrorMsg());
			$mailtext = sprintf(CFG_EMAIL_CREATEACCOUNT,$obj['name'],$obj['email'],$obj['login'],$obj['password']);
			mail($obj['email'],"Timemachine - Your Account",$mailtext,"FROM:Timemachine <noreplay@localhost.de>");
			
		} else {
			$json = new Services_JSON();
			echo $json->encode(array("error" => $error));
		}
	}
	
	/**
	 * login method expecting object $obj containing var username as string
	 * and password as md5 encrypted string
	 *
	 */
	function login ($obj) {
		if(!isset($obj->u_login) or !isset($obj->u_password) or !$obj->u_login or !$obj->u_password) {
			return array('result' => 'failure', 'reason' => 'error: parameter_missing');
		
		} else {
			$users = new User();
			$user = $users->find("u_login = '".mysql_escape_string($obj->u_login)."' and `u_password` = '".mysql_escape_string($obj->u_password)."' LIMIT 1");
			
//			$query="select * from `".$this->table."` where u_login = '".mysql_escape_string($obj->u_login)."' and `u_password` = '".mysql_escape_string($obj->u_password)."' LIMIT 1";
//			$user = $_SESSION['db']->getArray($query);
			
//			$this->db_fields = array();
			if(isset($user[0])) {
				foreach($user[0] as $k => $v) {
					if(property_exists(get_class($this),$k)) {
						$this->db_fields[] = $k;
						$this->$k = $v;
					}
				}
				
				if($this->u_working_on) {
					$currenttask = new Task($this->u_working_on);
					
					$this->u_working_project = $currenttask->project_id;
					$this->u_working_description = $currenttask->t_description;
				}
				
				$_SESSION['joel']->skinpath = 'skins/'.$this->u_skin.'/';
				$_SESSION['joel']->plugins_apply('_login');
				
				$result['result'] = 'success';
				$result['userdata'] = $this->getData();
				$_SESSION['joel']->loadPlugins();
				
			} else {
				$result['result'] = 'failure';
				$result['reason'] = 'no account found.';
			}
			
			return $result;
		}
	}
	
	
	public function helloMirko($data) {
		print_r($data);
		return array();
	}
	
	
	function getData() {
		$return = array();
		$private = array('u_password','db_fields');
		foreach($this as $e => $v) {
			if(!in_array($e, $private)) {
				$return[$e] = $v;
			}
		}
		
		return $_SESSION['joel']->plugins_apply('_user_getData',$return);
	}
	
	
	function get() {
		$return = array();
		$projects = new Project();
		
		$return = $_SESSION['db']->getArray("select * from `".$this->table."` where user_id = '".$this->user_id."'");
		$bookmarks = explode(',', $user['u_bookmarks']);
		$user['u_bookmarks'] = array();
		foreach($bookmarks as $k => $b) {
			if($b){
				$p = $projects->get($b);
				$user['u_bookmarks'][] = $p->title;
			}
		}
		
		return $this->joel->plugins_apply('_user_getData',$return[0]);
	}
	
	function getAll() {
		$return = array();
		$return = $_SESSION['db']->getArray("select * from `".$this->table."`");
		
		return $_SESSION['joel']->plugins_apply('_user_getAll',$return);
	}
	
	function logout ($data) {
		// close open sessions for this user
		$_SESSION['db']->execute("update set `".$this->table."` set s_end = '".time()."' where user_id = ".$this->user_id);
		$this->user_id = false;
		unset($this->userid);
	}
	
	function check() {
		if(isset($this->user_id)) {
			$this->access =  true;
			return true;
		} else {
			$this->access = false;
			return false;
		}
	}
}