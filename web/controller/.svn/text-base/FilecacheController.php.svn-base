<?php 

class FilecacheController {
	public $folder = "/web/cache";
	
	public function __construct  () {
		$this->path = dirname(dirname(dirname(__FILE__)))."/".$this->folder;
		$this->setFolder($this->folder);
	}
	
	public function setFolder($folder) {
		$newpath = dirname(dirname(dirname(__FILE__))).$folder;
		if(file_exists($newpath) && is_writeable($newpath)) {
			$this->path = $newpath;
			$this->folder = $folder;
		} else {
			throw new Exception($folder." is not writeable");
		}
	}
	
	public function exists($filename) {
		if(isset($_GET['refreshcache'])) return false;
		return file_exists($this->path."/".$filename);
	}
	
	public function getFile($filename, $href=false) {
		return $href ? $this->folder."/".$filename : $this->path."/".$filename;
	}
	
	public function getFileContents($filename) {
		if(file_exists($this->path."/".$filename)) {
			return file_get_contents($this->path."/".$filename);
		} else {
			return false;
		}
	}
	
	public function writeFile($filename, $content) {
		$fp = fopen($this->path."/".$filename, "w");
		fwrite($fp, $content);
		fclose($fp);
	}
	
	public function getPath($filename = '') {
		return $this->path."/".$filename;
	}
	
	public function getFolder($filename = '') {
		return $this->folder."/".$filename;
	}
}



?>