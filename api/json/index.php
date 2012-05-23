<?php

/**
* REST JSON Interface
* 
* @package API
*/

require_once("../../app/bootstrap.php");

$qs = explode("/", $_REQUEST['query']);

$controllerName = ucfirst($qs[0]).'Controller';
$methodName = isset($qs[1]) ? $qs[1] : "default";

// find Controller
if(!class_exists($controllerName)) {
	header('HTTP/1.1 404 Not Found');
	print 'Controller '.$controllerName.' does not exists!';
	exit;
}

if(isset($_SESSION['joel'])) {
	$controller = $controllerName == 'JoelController' ? $_SESSION['joel'] : new $controllerName();
}

// find Method Name
$methodNameCamelcased = "";
foreach(explode("-", $methodName) as $piece) {
	$methodNameCamelcased .= ucfirst($piece);
}
$methodName = lcfirst($methodNameCamelcased);

// manage request object
unset($_REQUEST['PHPSESSID']);
unset($_REQUEST['query']);

if(!method_exists($controller, $methodName)) {
	header('HTTP/1.1 404 Not Found');
	print 'Method "'.$methodName.'" does not exists in controller "'.$controllerName.'"';
	exit;
}

$return = $controller->$methodName($_REQUEST);



// show response as json encoded string
if($return) echo json_encode($return);

?>