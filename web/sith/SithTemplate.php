<?php
// $Id: SithTemplate.php 717 2009-01-03 17:03:10Z piotrlegnica $
/**
 * @mainpage SithTemplate - open-source template engine for PHP5
 * @section links Project information
 * @par Project homepage:
 * http://piotrlegnica.one.pl/SithTemplate
 *
 * There you can find all informations related to project.
 * @section license License
 * SithTemplate is released under terms and conditions of New BSD License.
 * You can find full text of this license in the "LICENSE" file.
 */
/**
 * Version of SithTemplate
 */
define('SITHTEMPLATE_VERSION', '1.0');

if (!defined('SITHTEMPLATE_DIR')) {
 /**
  * Path to SithTemplate files.
  */
 define('SITHTEMPLATE_DIR', pathinfo(__FILE__, PATHINFO_DIRNAME).'/');
}
/**
 * SPL autoloader for SithTemplate
 * @param $cls Class to load
 * @since 0.4.0
 */
function sithtemplate_spl_autoload($cls) {
 /**
  * Class => file autoloader map (array).
  */
 static $_autoload_map = array(
  // default implementation classes
  'templatemanager' => 'Manager.php',
  'templatelibrary' => 'Library.php',
  'templatelibraryloader' => 'LibraryLoader.php',
  'templatesyntaxerror' => 'Exceptions.php',
  'templateruntimeerror' => 'Exceptions.php',
  'templateerror' => 'Exceptions.php',
  'templatecompiler' => 'Compiler.php',
  'templatecompilerstate' => 'Compiler.php',
  'templatevalidator' => 'Compiler.php',
  'templatebase' => 'Base.php',
  'templatedefaultio' => 'IO.php',
  // interfaces
  'itemplatecompiler' => 'Interfaces.php',
  'itemplatemanager' => 'Interfaces.php',
  'itemplatecompilerstate' => 'Interfaces.php',
  'itemplatelibraryloader' => 'Interfaces.php',
  'itemplatelibrary' => 'Interfaces.php',
  'itemplatecompilerlibrary' => 'Interfaces.php',
  'itemplateruntimelibrary' => 'Interfaces.php',
  'itemplateio' => 'Interfaces.php',
  'itemplatevalidator' => 'Interfaces.php',
  'itemplatei18n' => 'Interfaces.php',
  // utils
  'templateutils' => 'Utils.php',
 );
 $cls = strtolower($cls);
 if (!isset($_autoload_map[$cls]) || !is_readable(SITHTEMPLATE_DIR.$_autoload_map[$cls])) {
  return false;
 }
 include_once $_autoload_map[$cls];
 return true;
}
// if SITHTEMPLATE_NO_AUTOLOADER is defined, then don't register autoloader with SPL
if (!defined('SITHTEMPLATE_NO_AUTOLOADER')) {
 spl_autoload_register('sithtemplate_spl_autoload');
}

/* if SITHTEMPLATE_USE_REFLECTION is not defined, then check for exitence of ReflectionClass,
   and use result as value for the constant. you will want to have this disabled (== false) when
   using sithtemplate in an optimized environment) */
if(!defined('SITHTEMPLATE_USE_REFLECTION')) {
 define('SITHTEMPLATE_USE_REFLECTION', class_exists('ReflectionClass', false));
}

// and ready

