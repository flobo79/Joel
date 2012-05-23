<?php

require_once (dirname(dirname(__FILE__))."/app/bootstrap.php");


if(isset($_GET['combined']) ) {
	$render = false;
	$cache = new Filecache;
	
	header ("content-type: text/css; charset=UTF-8");
	header ("cache-control: must-revalidate; max-age: 3600");
	header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 3600) . " GMT");
	
	if(!DEV && $cache->exists('styles.css')) {
		echo $cache->getFileContents('styles.css', true);
		
	} else {
		echo $content;
	}
	
} else {
	
	
	
	if (DEV) {
		foreach(collectFiles() as $file) {
			echo '	<link href="'.$file.'" rel="stylesheet" type="text/css" />'."\n";
		}
	} else {
		
		$cache = new Filecache;

		if(!$cache->exists('styles.css')) {
			$content = "/*  JOEL CSS COLLECTION */ \n\n";
			foreach(collectFiles() as $file) {
				$content .= file_get_contents(BASEDIR."/".$file);
			}
			
			$cache->writeFile('styles.css', preg_replace('/url\(/', 'url(/'.CLIENT.'/skins/'.SKIN.'/', $content));
		}
		
		echo '	<link href="'.$cache->getFolder('styles.css').'" rel="stylesheet" type="text/css" />'."\n";
	}
}



function collectFiles () {
	$files = array();
	$plugins = $_SESSION['joel']->pluginslist;
	$files['skin'] = "web/skins/".SKIN."/styles.css";
	
	foreach($plugins as $plugin) {
		$file = "plugins/".$plugin."/templates/styles.css";
		if(file_exists(BASEDIR."/".$file)) {
			$files[$plugin] = $file;
		}
	}
	
	return $files;
}
