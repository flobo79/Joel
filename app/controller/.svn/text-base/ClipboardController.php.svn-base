<?php 

class Clipboard {
	function Clipboard () {
		global $db;
		$this->db = $db;
		$this->table = TP.'_clipboard';
	}
	
	/**
	 * Reads a Clipboard entry and returns content
	 * 
	 * @return clipboard object
	 * @param $key String 
	 */
	function get($key) {
		global $json;
    	if(!isset($json)) $json = new Services_JSON;
		$entry = $_SESSION['db']->getArray("select * from `".$this->table."` where `cb_key` = '".mysql_real_escape_string($key)."' LIMIT 1");
		return isset($entry[0]) ? ereg("^{",$entry[0]['cb_content']) ? $json->decode($entry[0]['cb_content']) : $entry[0] : false;
	}
	
	/**
	 * sets a new clipboard entry
	 * 
	 * @return clipboard key
	 * @param $content - assoziative array or object
	 * @param $k String[optional] - optional key, otherwise key is being set automatically
	 */
	function set($obj,$k=false) {
		global $json;
    	if(!isset($json)) $json = new Services_JSON;
		if(!$k) $k = substr(md5(microtime()),0,10);
		
		$_SESSION['db']->execute("insert into `".$this->table."` set `cb_date` = '".time()."', `cb_key`='$k', `cb_content` = '".mysql_real_escape_string($json->encode($obj))."'");
		
		return $k;
	}
	
	/** 
	 * deletes an entry from the clipboard
	 * 
	 * @return Bool
	 * @param $key String Key for clipboard entry to be removed
	 */
	function delete($key) {
		return $_SESSION['db']->execute("delete from `".$this->table."` where `cb_key`='$key' LIMIT 1");
	}
}


?>