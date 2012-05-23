<?php
/** 
 * User Class
 * 
 */
class UserController extends BaseController {
	
	var $controller = "User";
	
	function UserController () {
		$this->Basecontroller();
	}
	
  	/**
	 * expects the fields to be updated as an object:
	 * $obj[fields] = object("u_name" => "Charles Bronson"[,...]);
	 */
	function update ($obj) {
		if($obj['user_id'] != $this->joel->user && !$this->isAdmin()) {
			return array('result' => 'failure', 'reason' => 'access denied');
		}
		
		$users = new User();
		$users->update($obj, $obj['user_id']);

		if($obj['user_id'] == $this->joel->user->user_id) {
			$user = $this->joel->user;
		}
		
		
		return array('result' => 'success');
	}
	
	/**
	 * 
	 * @param $id
	 * @return unknown_type
	 */
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
		if($obj['name'] == '') $error[] = "name missing";
		if($obj['email'] == '') $error[] = "email missing";
		if($obj['login'] == '') $error[] = "login missing";
//		if(!$this->isValidEmail($obj['email'])) $error[] = "email not valid";
		
		
		// sanitize input
		if(count($error) == 0) {
			$name = ($obj['name']);
			$email = ($obj['email']);
			$login = ($obj['login']);
			
			$users = new User();
			$user = $users->find("u_email = '{$email}'");
			if($user[0]) {
				$error[] = "email exists";
			}
			
			// check if username exists
			$user = $users->find("u_login = '{$login}'");
			if($user[0]) {
				$error[] = "login exists";
			}
		}
		
		
		if(count($error) == 0) {
			$new_user = $users->insert(array(
				'u_name' => $name,
				'u_email' => $email,
				'u_password' => md5($obj['password'].SALT),
				'u_login' => $login,
				'u_skin' => DEFAULTSKIN,
				'u_locale' => DEFAULT_LOCALE
			));
			
			$mailtext = sprintf($this->localize('Email New Account', $obj['name'], $obj['email'], $obj['login'], $obj['password']));
			$from = "FROM:Joel <noreplay@{DOMAIN}.de>";
			
			mail($obj['email'], $this->localize("Joel - Your Account"), $mailtext, $from);
			
			return array("result" => "success", 'user' => $new_user);
		} else {
			return array("result" => "error", "error" => $error);
		}
	}
	
	
	
	/**
	 * returns all user without password to admin users
	 * 
	 * @return array
	 */
	function getAll($all=false) {
		if(!$this->isAdmin() && !$all) return array('result' => 'failure', 'reason' => 'no rights to perform this action');
		
		$users = new User();
		$return = $users->getAll();
		
		
		// remove passwords
		foreach($return as $k => $r) { 
			unset($r['u_password']);
			$return[$k] = $r;
		}
		//$return = $this->plugins_apply('_user_getAll',$return);
		
		return $return;
	}
	
	function load($obj) {
		$users = new User();
		$user = $users->get($obj['user_id']);
		
		if($user->u_type != 'admin' && $user->user_id != $obj->user_id) return array('result' => 'failure', 'reason' => 'no rights to perform this action');
		
		// remove passwords
		foreach($user as $k => $r) { 
			unset($r->u_password);
			$return[$k] = $r;
		}
		
		$list = explode(',', trim($return['u_bookmarks'],','));
		$bookmarks = array();
		$projects = new Project();
			
		foreach($list as $bookmark) {
			$project = $projects->get($bookmark);
			
			$bookmarks[] = array(
				'project_id' => $bookmark,
				'p_name' => $project->p_name 
			);
		}
		
		$return['u_bookmarks'] = $bookmarks;
		//$return = $this->joel->plugins_apply('_user_getAll',$return);
		
		return $return;
	}
	
	function isAdmin() {
		return false;
		
	}
}