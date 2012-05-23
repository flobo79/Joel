<?php
/**
 * Utilities used throughout SithTemplate.
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Utils.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateUtils {
 /**
  * Escape string to use in template class.
  * @param $str String
  * @return Escaped string
  */
 public static function escape($str) {
  $str = str_replace('\'', '\\\'', $str);
  $str = str_replace(array("\n", "\r", "\t"),
                     array('\'."\\n".\'', '\'."\\r".\'','\'."\\t".\''), $str);
  return $str;
 }
 /**
  * Sanitize string, for use as function name.
  * @param $str String
  * @return Sanitized string
  */
 public static function sanitize($str) {
  return preg_replace('/([^a-z0-9\_])/i', '_', $str);
 }
 /**
  * Strip newlines and spaces from string.
  * @param $str String
  * @return Stripped string
  */
 public static function strip($str) {
  return str_replace(array("\n", ' '), '', $str);
 }
 /**
  * Split string into two.
  * @param $separator Separator
  * @param $str String to split
  * @param $reverse Use reversed search
  * @return Array
  */
 public static function split($separator, $str, $reverse = false) {
  $reverse = ($reverse ? 'strrpos' : 'strpos');
  $offset = strlen($separator);
  $separator = $reverse($str, $separator);
  if ($separator === false) {
   return array($str, '');
  }
  return array(
   substr($str, 0, $separator),
   substr($str, $separator+$offset)
  );
 }
 /**
  * Does class implements given interface
  * @param $classOrObject Mixed
  * @param $interface String
  * @return Boolean
  */
 public static function doesImplement($classOrObject, $interface) {
  return in_array($interface, class_implements($classOrObject));
 }
 /**
  * Checks whether element is allowed
  * @param $settings Array
  * @param $element String
  * @param $type String
  * @return Boolean
  */
 public static function isAllowed(array &$settings, $element, $type) {
  if (!$settings['enableSecurity']) return true;
  $onList = in_array($element, $settings[$type]);
  switch ($settings['securityPolicy']) {
   case TemplateManager::SECURITY_WHITE_LIST:
    return $onList;
   case TemplateManager::SECURITY_BLACK_LIST:
    return !$onList;
   default:
    return false; // control should never reach this place
  }
 }
}
