<?php

/** 
 * checks whether an object is an iterable
 * 
 * @return bool
 * @param object $obj
 */
function is_iterable ($obj) {
	return (is_object($obj) || is_array($obj)) ? true : false;
}


/**
* Helper to create an empty class object.
*/
class emptyClass {
	function destroy() {
		unset($this);
	}
}


/**
 * JSON in case Json is not available natively
 */
if (!function_exists('json_encode')) {
	function json_encode($obj) {
		$json = new Services_JSON();
		return $json->encode($obj);
	}
	function json_encode($obj) {
		$json = new Services_JSON();
		return $json->decode($obj);
	}
}


if(false === function_exists('lcfirst')) {
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str ) {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}

function first($str) {
	return substr($str,0,1);
}

function isValidEmail($email){
	$isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
//	      
//	      if ($isValid && !(checkdnsrr($domain,"MX") ||  checkdnsrr($domain,"A")))  {
//	         // domain not found in DNS
//	         $isValid = false;
//	      }
   }
   
   return $isValid;
}
