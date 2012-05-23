<?php
/** 
 * User Class
 * 
 * 
 */
class AuthController extends BaseController {
	var $public = true;
	var $controller = "Auth";
	
	function AuthController () {
		parent::__construct();
	}
	
	
	/**
	 * login method expecting object $obj containing var username as string
	 * and password as md5 encrypted string
	 *
	 */
	function login ($obj) {

		if(!isset($obj['login']) or !isset($obj['password']) or !$obj['login']) {
			return array('result' => 'failure', 'reason' => $this->_localize("parameters missing"));
		
		} else {
		
			$users = new User();
			$user = $users->find("u_login = '".mysql_escape_string(substr($obj['login'], 0, 50))."' and `u_password` = '".md5($obj['password'].SALT)."' LIMIT 1");
			
			if($user[0]) {
				$user = (object)$user[0];
				
				if($user->u_working_on) {
					$tasks = new Task();
					$task = $tasks->get($user->u_working_on);
					$user->u_working_project = $task->project_id;
					$user->u_working_description = $task->t_description;
				}
				
				
				$this->joel->user = $user;
				$this->joel->skinpath = 'skins/'.$this->u_skin.'/';
				//$_SESSION['joel']->plugins_apply('_login');
				
				$result['result'] = 'success';
				$result['userdata'] = $this->joel->user;
				unset($result['userdata']['u_password']);
				
				$_SESSION['joel'] = $this->joel;
				//$_SESSION['joel']->loadPlugins();
				
			} else {
				$result['result'] = 'failure';
				$result['reason'] = $this->_localize("no account found");
			}
			
			return $result;
		}
	}
	
	
	/**
	 * logs user out and resets joel session
	 * 
	 * @param $data
	 * @return unknown_type
	 */
	function logout ($data) {
		$this->joel->reset();
		return array('result' => 'success');
	}
}