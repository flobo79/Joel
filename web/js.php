<?php

require_once (dirname(__FILE__)."/bootstrap.php");

$render = false;
$cachefile = CLIENT_BASEDIR."/cache/javascript.js";


// get cached file
if(!DEV && file_exists($cachefile)) {
	if($time = time() - filectime($cachfile) < 3600) {
		$render = true;
	}
} else {
	$render = true;
}

$render = true;
if(!$render) {
	die("<script type=\"text/javascript\" src=\"cache/javascript.js\"></script>\n");
}

// get all js files and put mootools into first pos
$jsfiles = array("web/js/mootools.js");
$jsdir = opendir(CLIENT_BASEDIR."/js/") or die();
while (false !== ($file = readdir($jsdir))) {
	if(!preg_match('/^\.+/', $file) && !preg_match("/^mootools/", strtolower($file))) {
		$jsfiles[] = "web/js/".$file;
	}
}

// load all plugin js files
$jsplugins = array();
$jspluginslist = array();
foreach($_SESSION['joel']->pluginslist as $plugin) {
	if(file_exists($file = BASEDIR."/plugins/".$plugin."/plugin.js")) {
		$jsplugins[] = $file;
		$jspluginslist[]  = $plugin;
	}
}


// javascript variables
$js = ""; 
$js .= "	var skinpath = '".$_SESSION['joel']->skinpath."';\n";
$js .= "	var sessionID = '".session_id()."';\n";

foreach($jsconfig as $k => $v) { $js .= '		var '.$k."='".$v."';\n"; }
$js .= " var pluginslist = ['".implode("','", $jspluginslist)."'];\n";
$js .= " window.addEvent('domready', bootstrap); ";




/*** IF JAVASCRIPT FILE HAS BEEN LOADED AS SEPARATE FILE ***/
if(isset($_GET['c'])) {
	header('Content-Type: text/javascript; charset=utf-8');
	$files = "";
	foreach($jsfiles as $file) {
		$files .= "\n//-- include file /app/js/".$file."\n".stripcomments(file_get_contents(BASEDIR."/".$file))."\n\n";
	}
	
	foreach($_SESSION['joel']->pluginslist as $plugin) {
		$file = BASEDIR."/plugins/".$plugin."/plugin.js";
		
		if(file_exists($file)) {
			$files .= stripcomments(file_get_contents($file))."\n";
			#$pluginname = trim(str_replace('var','',substr($content,0,strpos($content, '='))));
			#$pluginslist .= $pluginname.":". substr($content, strpos($content,'{'), strrpos($content,'}')-strpos($content,'{')+1).",\n\n";
		}
	}
	
	$js = $files."\n\n".$js."\n";
	
	if(COMPRESS_JS) {
		require 'app/php/class.JavaScriptPacker.php';
		header ("cache-control: must-revalidate; max-age: 86400");
		header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 86400) . " GMT");
		$packer = new JavaScriptPacker($js, 'Normal', true, true);
		$js = $packer->pack();
	}
	
	//$cache = new Filecache;
	//$cache->writeFile('scripts.js', $js);
	
	echo $js;
	
} else {
	if(DEV) {
		// in dev mode load all files seperately
		foreach($jsfiles as $f) {  		echo "	<script type=\"text/javascript\" src=\"$f\"></script>\n";  }
		foreach($jspluginslist as $f) {  	echo "	<script type=\"text/javascript\" src=\"plugins/$f/plugin.js\"></script>\n"; }
		
		echo "<script type=\"text/javascript\">\n";
		echo " $js";
		echo "\n</script>\n";
		
	} else {
		echo "<script type=\"text/javascript\" src=\"web/js.php?c=true\"></script>\n";
	}
}


function stripcomments($str) {
	return trim(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','', $str));
}



?>
