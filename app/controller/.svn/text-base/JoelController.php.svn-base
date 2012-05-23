<?php

/**
 * Base Class of Joel
 *
 * @package Joel
 * @author Florian Bosselmann <bosselmann@gmail.com>
 * @category   Testing
 * @copyright  2002-2008 Florian Bosselmann <bosselmann@gmail.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://code.google.com/p/joel
 * @since      Class available since Release 1
*/

class JoelController extends Basecontroller {
	var $plugins;
	var $pluginslist;
	var $basedir;
	var $modifiers;
	var $skinpath = "skins/blue/"; // default skin path, can be overwritten ie by the user object
	var $user;
	var $project;
	var $skin = DEFAULTSKIN;
	var $client = CLIENT;
	
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function JoelController() {
		$this->pluginslist = array();
		$this->basedir = dirname(dirname(dirname(__FILE__)));
		$this->user = new User();
		
		if(!$this->plugins) $this->loadPlugins();
	}
	
	
	/**
	 * method to be called to get status information about the
	 * current Joel initialisation status
	 * 
	 * @return 
	 */
	public function bootstrap($data) {
		$this->client = $data['client'] ? $data['client'] : 'web';		

		return array(
			'sessid'=>session_id(),
			'loggedin'=>$this->user->user_id,
			'skin' => $this->skin,
			'client' => $this->client
		);
	}
	
	
	public function reset() {
		$_SESSION['joel'] = false;
		$_SESSION['joel'] = new JoelController();
	}
	
	
	/** 
	 * this method reads the plugins directory and creates
	 * a list of plugins
	 *
     * @param  string  $filename
     * @param  boolean $syntaxCheck
     * @throws RuntimeException
     * @access public
     * @static
     */
	  function loadPlugins() {
		$level = ($this->user->u_type == 'admin') ? '2' : (isset($_SESSION['joel']->user->user_id) ? 1 : 0);
		$this->pluginslist = array();
		$this->plugins = array();
		
		$dir = $this->basedir."/plugins";
		if (is_dir($dir)) {
			
		    if ($dh = opendir($dir)) {
		        while (($entry = readdir($dh)) !== false) {
		        	if($entry != '.' && $entry != '..') {
						if(file_exists($dir."/$entry/plugin.php")) {
							include_once($dir."/$entry/plugin.php");
							
							if(class_exists($classname="Plugin_".$entry."Controller")) {
								$_plugin = new $classname();
								
								if($_plugin->permission <= $level) {
									$this->pluginslist[] = $entry;
									$this->plugins['plugin_'.$entry] = new $classname();
								}
							}
						} else {
							$this->pluginslist[] = $entry;
						}
		        	}
		        }
		        closedir($dh);
		    }
		}
	}
	
	
	/**
	 * method to save user details
	 *  
	 * @return String "success" after saving
	 * @param $obj Object
	 * 
	 * @access 
	 * @package Joel
	 */
	function user_update($obj) {
		$this->user->update($obj);
	}
	
	
	
	function getTasklist($obj) {
		if(!$this->user->user_id) die("logout");
		$this->project->load($obj->project);
		$this->user->addBookmark($obj->project);
		$tasklist = new Tasklist($obj);
		$tasklist -> getTasklist($obj);
	}
	
	/**
	 * updates a particular field of a task
	 * 
	 * @return nothing
	 * @param $obj Object
	 * @package Joel
	 */
	function task_updateField($obj) {
		$task = new Task($obj->task_id);
		$task->updateField($obj);
	}
	
	function tasklist_updateOrder($obj) {
		Tasklist::updateOrder($obj);
	}
	
	
	
	/**
	 * creates a new task 
	 * 
	 * @return 
	 * @param $obj Object containing optional parameters: projectID
	 * @access 
	 * @package Joel
	 */
	
	/*
	function task_create($obj=false) {
		$task = new Task($obj);
		unset($task->db, $task->table, $task->db_fields);
		return $this->plugins_apply('_task_create', $task);
	}
	
	*/
	
	
	/**
	 * This method returs a list of Projects the user is allowed to 
	 * work on.
	 * 
	 * @return JSON String of projects list or "logout"
	 * @access 
	 * @package Joel
	 */
	/*
	function getProjects($param = false) {
		if($this->user->user_id) {
			$projects = array();
			$sql = "select 
					ROUND(SUM(t.t_currest)) as currest, 
					ROUND(SUM(t.t_origest)) as origest, 
					ROUND(SUM(t.t_elapsed)) as elapsed, 
					ROUND(SUM(t.t_currest) - SUM(t.t_elapsed)) as remain, 
					p.p_name, p.project_id, p.collection_id 
				from 
					".TP."_projects as p 
					left join ".TP."_tasks as t 
					on t.project_id = p.project_id 
				where p.user_id = ".$this->user->user_id." ";
				
				if(isset($param->collection_id) && intval($param->collection_id)) {
					$sql .= "and p.customer_id = ".$param->collection_id." ";
				}
				 
				$sql .= "group by 
					p.project_id 
				order by 
					p.p_name";
				
			$projects =  $_SESSION['db']->getArray($sql);
			$bookmarks = explode(',', $this->user->u_bookmarks);
			$projects = $this->plugins_apply('_getProjects', $projects);
			
			if(is_array($bookmarks)) {
				foreach($projects as $k => $project) {
					$projects[$k]['bookmarked'] = in_array($project['project_id'],$bookmarks) ? 1 : 0;
				}
			}
			
			return $projects;
			return;
		}
		echo "logout";
		return;
	}
	*/
	
	/**
	 * update collection
	 *
	 * @return nothing
	 */
	public function collection_create($obj) {
		$collection = new Collection($obj);
	}
	
	/**
	 * deletes a collection and all timesheets
	 * 
	 * @return 
	 * @param object $obj
	 */
	public function collection_delete($obj) {
		$collections = new Collection();
		$collections->delete($obj->cusomter_id);
	}
	
	/**
	 * update collection
	 *
	 * @return nothing
	 */
	public function collection_update($obj) {
		$collection = new Collection($obj->collection_id);
		if($collection->collection_id) {
			$collection->update($obj);
		} 
	}
	
		
	/**
	 * get all collections
	 * 
	 * @return 
	 */
	
	public function collection_getlist() {
		$collectionlists = new Collection();
		$list = $collectionlists->getList();
		
		$list = $this->plugins_apply('_getcollectionlist',$list);
		return $list;
	}
	
	
	/**
	 * method to remove a bookmark from a users bookmarks list
	 * 
	 * @return nothing
	 * @param $obj Object containing the project ID
	 * 
	 * @access 
	 * @package Joel
	 */
	 function deleteBookmark ($obj) {
		$this->user->deleteBookmark($obj->id);
	}
	
	/**
	 * deletes a set of tasks
	 * 
	 * @return 
	 * @param object $obj
	 */
	function tasklist_delete($obj) {
		$tasklist = new Tasklist();
		$tasklist->deleteTasks($obj);
	}
	
	/**
	 * copies a set of tasks
	 * 
	 * @return 
	 * @param object $obj
	 */
	function tasklist_copyTasks($obj) {
		$tasklist = new Tasklist();
		$tasks = $tasklist->copyTasks($obj);
		$tasks = $this->plugins_apply("_tasklist_copyTasks", array($obj->tasks, $tasks));
		
		// if it was a cut action - delete original task
		if($obj->delete) {
			foreach($obj->tasks as $id) {
				$tasklist->deleteTask($id);
			}
		}
		
		return $tasks;
	}
	
	
	/**
	 * method to paste a set of new task at once into a project, and returns 
	 * the json encoded list of new rows
	 * 
	 * @return json encoded list of new tasks
	 * @param $obj Object
	 * 
	 * @access public
	 * @package Joel
	 */
	function task_create_bulk($obj) {
		$newtasks = array();
		$insertafterid = $obj->insertafterid;
		
		foreach($obj->block as $row) {
			$param = new emptyClass();
			$param->t_description = $row;
			$param->insertafterid = $insertafterid;
			$newtask = $this->plugins_apply('_task_create', new Task($param));
			$newtasks[] = $newtask;
			$insertafterid = $newtask -> task_id;
		}
		
		return $newtasks;
	}
	
	/**
	 * applies a possible existing plugin callback modifier to an object
	 * 
	 * @return modified object
	 * @param $obj Object to be modified
	 * @param $modifier String modifier name
	 */
	function plugins_apply($modifier, $obj=false) {
		/*
		foreach($this->plugins as $plugin) {
			if(method_exists($plugin, $modifier)) {
				$obj = $plugin->$modifier($obj);
			}
		}
		return $obj;
		*/
	}
	
	/**
	 * Starts the timer, by storing timer information with the user, to be picked
	 * up after a reload or a later session.
	 * 
	 * @return nothing
	 * @param $obj Object
	 * @access 
	 * @package Joel
	 */
	 function Stopwatch_start($obj) {
	 	
		/* if the user is already working on a task, stop that and save the elapsed time */
		if($this->user->u_working_on) {
			$currtask = new Task($this->user->u_working_on);
			$currtask->update(array('t_elapsed' => round(time() - $this->user->u_working_since)));
			unset($currtask);
		}
		
		if(isset($obj->task_id)) {
			/* get Task to be worked on */
			$task = new Task($obj->task_id);

			/* write new working task to users db */
			$data = new emptyClass();
			$data->working_on = $obj->task_id;
			$data->working_since = time()-($task->t_elapsed);
			$data->working_project = $task->project_id;
			$data->working_description = substr($task->t_description,0,200);
			
			$this->user->update($data);
		}
   }
	
	/**
	 * Stops a counting timer
	 * 
	 * @return nothing
	 * @param $obj Object
	 * @access 
	 * @package Joel
	 */
	function Stopwatch_stop() {
		$task = new Task($this->user->u_working_on);
		if($task->task_id) {
			$update = array('t_elapsed' => time() - $this->user->u_working_since);
			if($task->t_currest < $update['t_elapsed']) $update['t_currest'] = $update['t_elapsed'];
			$task->update($update);
		}
		
		$user = new emptyClass();
		$user->working_on = 0;
		$user->working_since = 0;
		$user->working_project = 0;
		$user->working_description = 0;
		
		$this->user->update($user);
	}
	
	/**
	 * escapes a string to be mysql safe
	 * 
	 * @return mysql secure string
	 * @param $str - String to be masked
	 * @access private
	 * @package Joel
	 */
	 function castString ($str) {
		if(get_magic_quotes_runtime()) { $str = stripslashes($str); }
		return mysql_real_escape_string($str);
	}
   
	/**
	 * creates a javascript block and puts javascript code into it
	 * to be executed in the client. The input has to be an array with each
	 * js-code line in a seperate field
	 * 
	 * @return 
	 * @param $array Array containing javascript code
	 * @access private
	 * @package Joel
	 */
	 function jsResponse($array) {
		if(is_array($array)) {
			echo '<script language="javascript">';
				implode('',$array);
			echo '</script>';
		}
	}
   
	
   /**
    * encodes an object and returns it to the client
    * 
    * @return JSON String
    * @param $packet Object to be json encoded
	* @package Joel
	* 
    */
    function jsonResponse($packet, $verbose = false) {
    	die("jsonResponse is depricated");
	}
	
	
	/** 
	 * This method checks if a table exists in database
	 * 
	 * Note: This is not the best place for this method, but i'm
	 * not shure how to extend the adodb framework
	 * 
	 * @return 
	 * @param $table Object
	 */
	 function table_exists($table) {
    	$exists = false;
        if (is_string($table)) {
        	$sql = 'select * from test_table';
			$resultset = $_SESSION['db']->Execute($sql);
		}

        return $exists;
    }
}

