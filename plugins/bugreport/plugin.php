<?php

/**
 * plugin help
 * 
 * @return
 * @package plugins
 */
class plugin_bugreportController extends Plugin {
	
	function loadWindow($obj) {
		echo file_get_contents(dirname(__FILE__)."/form.html");
	}
	
	
	
	function submit($obj) {
		mail(EMAIL, $obj['type'], "
Date: ".date("d-m-y",time())."

Message: ".$obj['message']."
System: ".$obj['report']."
","from: Joel <noreturn@joel.com>");

		echo '';
	}
}

?>
