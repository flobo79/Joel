<?php 


$basedir = str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(dirname(__FILE__))).'/api/json/';

require_once(dirname(dirname(__FILE__)).'/app/bootstrap.php');
$list = array();

$d = opendir(BASEDIR.'/app/controller');
while(($file = readdir($d)) !== false) {
	
	$controllername = substr($file,0, -4);
	$methods = get_class_methods ($controllername);
	if($methods[0]) {
		array_splice($methods,0,1);
		$list[$controllername] = $methods;
	}
}

$methods = array();

foreach($list as $k => $e) {
	foreach($e as $n) {
		if($n != 'Basecontroller' && first($n) != '_')
			$methods[] = $basedir.strtolower(substr($k, 0, -10)).'/'.$n;
			//echo $basedir.strtolower(substr($k, 0, -10)).'/'.$n.'<br>';
	}
}



?>