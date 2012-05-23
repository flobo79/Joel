<?php
/**
 * Core filter library.
 * @since 0.3.0
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: CoreFilter.library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class CoreFilterLibrary extends TemplateLibrary implements ITemplateRuntimeLibrary {
 /**
  * Return handlers for this library
  * @return Array
  */
 public static function getHandlers() {
  return array(
   'tags' => array(),
   'filters' => array(
    'add'            => 'handleAdd',
    'addslashes'     => 'handleAddSlashes',
    'capfirst'       => 'handleCapFirst',
    'cut'            => 'handleCut',
    'date'           => 'handleDate',
    'default'        => 'handleDefault',
    'divisibleby'    => 'handleDivisibleBy',
    'escape'         => 'handleEscape',
    'filesizeformat' => 'handleFileSizeFormat',
    'fix_ampersands' => 'handleFixAmpersands',
    'join'           => 'handleJoin',
    'length'         => 'handleLength',
    'length_is'      => 'handleLengthIs',
    'linebreaks'     => 'handleLineBreaks',
    'linebreaksbr'   => 'handleLineBreaksBR',
    'ljust'          => 'handleLJust',
    'lower'          => 'handleLower',
    'make_list'      => 'handleMakeList',
    'pluralize'      => 'handlePluralize',
    'random'         => 'handleRandom',
    'removetags'     => 'handleRemoveTags',
    'rjust'          => 'handleRJust',
    'slugify'        => 'handleSlugify',
    'title'          => 'handleTitle',
    'upper'          => 'handleUpper',
    'urlencode'      => 'handleURLEncode',
    'urldecode'      => 'handleURLDecode',
    'wordcount'      => 'handleWordCount',
    'wordwrap'       => 'handleWordWrap',
   ),
   'hooks' => array(),
  );
 }
 /** @filter{add} */
 public function handleAdd($val, $add) { 
  return ($val + $add);
 }
 /** @filter{addslashes} */
 public function handleAddSlashes($val) {
  return addslashes($val);
 }
 /** @filter{capfirst} */
 public function handleCapFirst($val) {
  if (extension_loaded('mbstring')) {
   return mb_strtoupper(mb_substr($val, 0, 1)).mb_substr($val, 1);
  } else {
   return ucfirst($val);
  }
 }
 /** @filter{cut} */
 public function handleCut($val, $cut) { 
  return preg_replace('~'.preg_quote($cut, '~').'~u', '', $val);
 }
 /** @filter{date} */
 public function handleDate($ts, $fmt) { 
  return date($fmt, (int)$ts);
 }
 /** @filter{default} */
 public function handleDefault($var, $default) { 
  return (!$var ? $default : $var);
 }
 /** @filter{divisibleby} */
 public function handleDivisibleBy($val, $dv) {
  $dv = (int)$dv;
  if ($dv == 0) return false;
  return (($val % $dv) == 0);
 }
 /** @filter{escape} */
 public function handleEscape($val) { 
  return htmlspecialchars($val);
 }
 /** @filter{filesizeformat} */
 public function handleFileSizeFormat($val) {
  $val = (float)$val;
  if ($val < 1024) {
   return $val.' b';
  } elseif ($val < 1024*1024) { // 1024*1024
   return round($val / 1024, 2).' kB';
  } elseif ($val < 1024*1024*1024) { // 1024*1024*1024
   return round($val / (1024*1024), 2).' MB';
  } else {
   return round($val / (1024*1024*1024), 2).' GB';
  }
 }
 /** @filter{fix_ampersands} */
 public function handleFixAmpersands($val) {
  return str_replace('&', '&amp;', $val);
 }
 /** @filter{join} */
 public function handleJoin($arr, $sep) { 
  return implode($sep, $arr);
 }
 /** @filter{length} */
 public function handleLength($val) { 
  if (is_string($val)) {
   return strlen($val);
  } else {
   return count($val);
  }
 }
 /** @filter{length_is} */
 public function handleLengthIs($val, $test) {
  if (is_string($val)) {
   return strlen($val) == $test;
  } else {
   return count($val) == $test;
  }
 }
 /** @filter{linebreaks} */
 public function handleLineBreaks($val) {
  // direct port of django.utils.html.linebreaks
  //value = re.sub(r'\r\n|\r|\n', '\n', force_unicode(value)) # normalize newlines
  //paras = re.split('\n{2,}', value)
  //paras = [u'<p>%s</p>' % p.strip().replace('\n', '<br />') for p in paras]
  $val = str_replace(array("\r\n", "\r", "\n"), "\n", $val); // normalize newlines
  $paras = preg_split("/\n{2,}/", $val);
  foreach ($paras as &$p) {
   $p = '<p>'.str_replace("\n", '<br />', trim($p)).'</p>';
  }
  return implode("\n\n", $paras);
 }
 /** @filter{linebreaksbr} */
 public function handleLineBreaksBR($val) {
  return str_replace("\n", '<br />', $val);
 }
 /** @filter{ljust} */
 public function handleLJust($val,$width) {
  return sprintf('%-'.((int)$width).'s', $val);
 }
 /** @filter{lower} */
 public function handleLower($val) { 
  if (extension_loaded('mbstring')) {
   $val = mb_strtolower($val);
  } else {
   $val = strtolower($val);
  }
  return $val;
 }
 /** @filter{make_list} */
 public function handleMakeList($val) {
  return str_split((string)$val);
 }
 /** @filter{pluralize} */
 public function handlePluralize($val,$suffix='s') { 
  if (strpos($suffix, ',') !== false) {
   list($ssuffix, $psuffix) = explode(',',$suffix);
   if ($val == 1) return $ssuffix;
   elseif ($val > 1) return $psuffix;
   return '';
  }
  if ($val > 1) return $suffix;
  return '';
 }
 /** @filter{random} */
 public function handleRandom($val) { 
  return $val[array_rand($val)];
 }
 /** @filter{removetags} */
 public function handleRemoveTags($val) {
  return strip_tags($val);
 }
 /** @filter{rjust} */
 public function handleRJust($val,$width) {
  return sprintf('%'.((int)$width).'s', $val);
 }
 /** @filter{slugify} */
 public function handleSlugify($val) { 
  if (extension_loaded('mbstring')) {
   $val = mb_strtolower($val);
  } else {
   $val = strtolower($val);
  }
  $val = strip_tags($val);
  $val = preg_replace('~\s+|\_~', '-', $val);
  $val = preg_replace('~\-+~', '-', $val);
  $val = preg_replace('~(^\-+)|(\-+$)|[^a-z0-9\-]~i', '', $val);
  return $val;
 }
 /** @filter{title} */
 public function handleTitle($val) {
  if (extension_loaded('mbstring')) {
   return mb_convert_case($val, MB_CASE_TITLE);
  } else {
   return ucwords($val);
  }
 }
 /** @filter{upper} */
 public function handleUpper($val) { 
  if (extension_loaded('mbstring')) {
   $val = mb_strtoupper($val);
  } else {
   $val = strtoupper($val);
  }
  return $val;
 }
 /** @filter{urlencode} */
 public function handleURLEncode($val) {
  return urlencode($val);
 }
 /** @filter{urldecode} */
 public function handleURLDecode($val) {
  return urldecode($val);
 }
 /** @filter{wordcount} */
 public function handleWordCount($val) {
  return str_word_count($val);
 }
 /** @filter{wordwrap} */
 public function handleWordWrap($val,$w) {
  return wordwrap($val,$w,"\n",true);
 }
}
