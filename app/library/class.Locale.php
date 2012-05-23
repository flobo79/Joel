<?php

class Locale {
	public $locale = DEFAULT_LOCALE;
	public $catalogue = array();
	
	function Locale($catalogue=false) {
		if($_SESSION['joel']->user) {
			$this->locale = $_SESSION['joel']->user->u_locale;
			$this->loadCatalogue($catalogue);
		}	
	}
	
	public function str($str) {
		return (string) ($translated = $this->catalogue[$str]) ? $translated : $str;
	}
	
	public function translateText($text) {
		foreach($this->catalogue as $k=>$v) { 
			$text = str_replace('#'.$v.'#', $v, $text);
		}
		
		return $text;
	}
	
	
	private function loadCatalogue($catalogue) {
		$cataloge = array();
		
		if ($catalogue) {
			if(($handle = fopen(LOCALE."/".$this->locale."/".ucfirst($catalogue).".csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			        $catalogue[$data[0]] = $data[1];
			    }
			    fclose($handle);
			}			    
		} else {
			$catalogue = array();
			$path = LOCALE."/".$this->locale;
			
			$d = dir($path);
		    while (false !== ($entry = $d->read())) {
		      if(is_file($path.'/'.$entry)) {
  		      if($handle = fopen($path.'/'.$entry, 'r')) {
  		    	 while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  			        $catalogue[$data[0]] = $data[1];
  			     }
  			     fclose($handle);
  		    	}
  				  else {
  				    break;
  				  }
		      }
			}
			$d->close();
		}
		
		print_r($catalogue);
		$this->catalogue = $catalogue;
	}
}


?>