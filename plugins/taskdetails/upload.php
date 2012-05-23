<?php

// include main lib
require (dirname(dirname(dirname(__FILE__)))."/bootstrap.php");

ob_start();

if (isset($_FILES['Filedata']) && 
	isset($_POST['PHPSESSID']) && 
	$_POST['PHPSESSID'] == session_id() && 
	isset($_POST['taskID']) && 
	$_SESSION['joel']->user->user_id && 
	intval($_POST['taskID'])) {
		
		$taskID = $_POST['taskID'];
		$plugindir = dirname(__FILE__)."/files/".$taskID;
		
		// check if task dir exists
		if(!file_exists($plugindir))  mkdir($plugindir);
		
		// filter filename for stupid characters
		$filename = str_ireplace(array(' ','ü','ä','ö','/','&','ß','%','-'),'',$_FILES['Filedata']['name']);
		$newfilename = $filename;
		
		$i=1;
		while(file_exists($plugindir."/".$newfilename)) {
			$newfilename = substr($filename,0,strrpos($filename,'.')).$i.substr($filename,strrpos($filename,'.'));
			$i++;
		}
		
		$file = $_FILES['Filedata']['tmp_name'];
			move_uploaded_file($file, $plugindir."/".$newfilename);
}
//mail("mailto","test",ob_get_contents(),"");
ob_end_clean();

echo "ok"; // flash needs some input

?>