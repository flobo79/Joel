<?php

/**
 * Provides the functionalities to invite other people to use JOEL
 * 
 * @package Plugin_ShareProjects
 * @author Florian Bosselmann
 * 
 */

class plugin_inviteController extends Plugin {
	var $cb_key = false;	// clipboard key
	
	function plugin_inviteController() {
		$this->table = TP.'_plugin_invite';
		/*
		$_SESSION['db']->execute("CREATE TABLE IF NOT EXISTS `".$this->table."` (
			`user_id` int(11) NOT NULL,
		  `invited_by` int(10) NOT NULL,
		  PRIMARY KEY  (`user_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		*/
	}
	
	/**
	 * loads the invite form
	 * @return 
	 */
	function loadTemplate () {
		echo file_get_contents(dirname(__FILE__)."/templates/form.html");
	}
	
	/**
	 * loads invitation details
	 * 
	 * @return 
	 * @param object $obj
	 */
	function loadInvitation($obj) {
		// load clipboard
		$clipboard = new Clipboad();
		if($invitation = $clipboard->get($obj->key)) {
			// get userdetails of sender from db
			$from = new User($invitation->from);
			// send details to frontend
			$_SESSION['joel']->jsonResponse(array('from' => $from->u_name));
		}
	}
	
	
	function createAccount($obj) {
		// first check if username is not beeing taken yet
		$db = $_SESSION['db'];
		$clipboard = new Clipboad();
		
		$checkUsername = $db->getArray("select user_id from `".TP."_user` where u_login = '".$db->QMagic($obj->packet->signup_username)."' LIMIT 1");
		if(isset($checkUsername[0])) {
			echo "Sorry, this username has been taken";
			return;
		}
		$cb = $clipboard->get($obj->packet->cb_key);
		
		// write user to db
		$sql = "insert into `".TP."_user` set 
			`u_name`= '".$db->QMagic($obj->packet->signup_name)."',
			`u_login` = '".$db->QMagic($obj->packet->signup_username)."',
			`u_password` = '".md5($obj->packet->signup_password.'Das weiss ich nicht')."',
			`u_email` = '".$db->QMagic($cb->to)."',
			`u_skin` = '".DEFAULTSKIN."'";
			
		$db->execute($sql);
		$id = $db->insert_ID();
		
		// now lets create the first project
		$newProject = new emptyClass();
		$newProject->title = 'My first project';
		
		// fake current user to use some tm functions...
		$_SESSION['joel']->user->user_id = $id;
		
		// ... like creating projects ...
		$_SESSION['joel']->project->create($newProject);
		
		// ... and delete the fake user id
		$_SESSION['joel']->user->user_id = false;
		
		$mailtext = "Thank you for signup up for a better Task tracking with \"Joel\".\n\n
your login details are:
	username: ".$obj->packet->signup_username."
	password: ".$obj->packet->signup_password."
		
Please keep these in a safe place.
		";
		
		$clipboard->delete($obj->packet->cb_key);
		
		mail($cb->to,'Your account details', $mailtext,'FROM:Joel <noreturn@'.$_SERVER['HTTP_HOST'].'>');
	}
	
	
	/**
	 * send request email and write request to clipboard
	 * 
	 * @return nothing
	 * @param $obj Object
	 */
	function sendInvitation($obj) {
		$clipboard = new Clipboard();
		
		$k = $clipboard->set(array(
			'to' => $obj->email,					// to email address
			'from' => $_SESSION['joel']->user->user_id	// from whom
		));
		
		$email = "Hi there,\n\n".$_SESSION['joel']->user->u_name." has invited you to use Joel to keep track of your tasks. Please click this link to accept this invitation:\n
http://".$_SERVER['HTTP_HOST'].PATH."/?invite=".$k;
		
		mail($obj->email,'Invitation from '.$_SESSION['joel']->user->u_name, $email, 'FROM:Joel Task Tracking <noreturn@'.$_SERVER['HTTP_HOST'].'>');
	}
}
	
?>
