<?php
require_once('unpack.lib.php');
define( 'PCLZIP_TEMPORARY_DIR', dirname(__FILE__));
$archive = new PclZip('Joel.zip');
 

function chmod_R($path, $filemode) {
	
    if (!is_dir($path)) {
		echo "file: ".$path."\n";
		return chmod($path, $filemode);

	} else {
  		echo "dir: ";
	    $dh = opendir($path);
	
	    while (($file = readdir($dh)) !== false) {
	        if($file != '.' && $file != '..') {
	            $fullpath = $path.$file;
				echo $fullpath."\n";
			
	            if(is_link($fullpath)) {
			 		return false;
				} 
			   
	            elseif(!is_dir($fullpath)) {
	                if (!chmod($fullpath, $filemode)) {
						//return FALSE;
					}
				}
	            elseif(!chmod_R($fullpath."/", $filemode))
				{
					return false;
				}
	        }
	    }

	    closedir($dh);
	
		return TRUE;
	}
}

if (($list = $archive->extract()) == 0) {
    die("Error : ".$archive->errorInfo(true));
} else {
	chmod_R(dirname(__FILE__)."/" ,0755);
	chmod(dirname(__FILE__)."/config.inc.php", 0777);
	//rename(__FILE__, dirname(__FILE__)."/install/")
	rename(dirname(__FILE__)."/Joel.zip", dirname(__FILE__)."/install/Joel.zip");
	//rename(dirname(__FILE__)."/Joel.zip", dirname(__FILE__)."/lib/Joel.zip");
	
}

?>