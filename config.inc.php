<?php

// Database Connection
$db_config['databasetype']		= 'mysql';
$db_config['hostname'] 			= "localhost"; 
$db_config['databasename']		= "joel";   
$db_config['username'] 			= "root";		
$db_config['password'] 			= "flobo";
$db_config['table_prefix'] 		= "j";


// Constants
define('CLIENT', 'web');
define('TP', $db_config['table_prefix']);
define('PROJECT_TITLE', 'Joel (beta)');
define('EMAIL','bosselmann@gmail.com');
define('DOMAIN', 'locahost');
define('DEFAULTSKIN','blue');
define('PATH', '/joel');
define('DEV', 1);
define('COMPRESS_JS',0);
define('CONTROLLER', BASEDIR.'/app/controller');
define('LIBRARY', BASEDIR.'/app/library');
define('LOCALE', BASEDIR.'/app/locale');
define('MODELS', BASEDIR.'/app/models');
define('SALT', 'das weiss ich nicht');
define('DEFAULT_LOCALE', 'en_US');


?>