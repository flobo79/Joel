<?php
/**
 * Default implementation of library loading and parsing routines.
 * @since 0.4.0
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: LibraryLoader.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateLibraryLoader implements ITemplateLibraryLoader {
 /**
  * Loaded libraries (array).
  */
 protected $libraries = array();
 /**
  * Enabled libraries (context-aware, array)
  */
 protected $enabledLibraries = array();
 /**
  * Core libraries list (array).
  */
 public $coreLibraries = array('CoreTag', 'CoreFilter', 'CoreHook');
 /**
  * Load and parse library.
  * @param $lib Library name (string)
  * @param $settings Settings (array)
  */
 public function loadLibrary($lib, array $settings) {
  if (($class = $this->findPlugin('Library', $lib, $settings)) === false) {
   throw new TemplateRuntimeError(TemplateError::E_UNKNOWN_LIBRARY, array($lib));
  }
  $compiledFile = $settings['compileDir'].'library.'.$class[0].'.meta';
  
  // parse and cache library
  if (!file_exists($compiledFile) || (filemtime($class[1]) > filemtime($compiledFile))) {
   $this->parseLibrary($lib, $class[0], $compiledFile);
  }

  $this->libraries[$lib] = unserialize(file_get_contents($compiledFile));
 }
 /**
  * Enable library in given context
  * @param $context Context name (string)
  * @param $lib Library name (string)
  */
 public function enableLibrary($context, $lib) {
  if (!isset($this->libraries[$lib])) {
   throw new TemplateRuntimeError(TemplateError::E_LIBRARY_NOT_LOADED, array($library));
  }
  if (!isset($this->enabledLibraries[$context])) {
   $this->enabledLibraries[$context] = array();
  }
  $this->enabledLibraries[$context][] = $lib;
 }
 /**
  * Parse library.
  * @param $lib Library name (string)
  * @param $class Classname of library (string)
  * @param $compiledFile Compiled library filename (filename)
  */
 protected function parseLibrary($lib, $class, $compiledFile) {
  // we take library class, use Reflection to get all methods
  // then parse each docblock to figure out
  // which methods handle which tag/filter
  //
  // prepare
  $handles = array(
   'filters' => array(),
   'tags' => array(),
   'hooks' => array(),
  );
  // fire up
  // if Reflection is unavailable or switched off, use static method getHandlers defined in ITemplateLibrary
  if (!SITHTEMPLATE_USE_REFLECTION) {
   $handles = call_user_func(array($class, 'getHandlers'));
   if (!is_array($handles)) {
    throw new TemplateRuntimeError(TemplateError::E_INVALID_DATA,
                                   array($class.'::getHandlers must return an array'));
   }
  } else {
   $library = new ReflectionClass($class);
   foreach ($library->getMethods() as $method) {
    $doc = $method->getDocComment();
    if (preg_match_all('/\@filter\{(.+?)\}/i', $doc, $matches, PREG_SET_ORDER)) {
     foreach ($matches as &$set) {
      $handles['filters'][$set[1]] = $method->getName();
     }
    } elseif (preg_match_all('/\@tag\{(.+?)\}/i', $doc, $matches, PREG_SET_ORDER)) {
     foreach ($matches as &$set) {
      $handles['tags'][$set[1]] = $method->getName();
     }
    } elseif (preg_match('/\@hook\{(.+?)\}/i', $doc, $matches)) {
     if (!isset($handles['hooks'][$matches[1]])) {
      $handles['hooks'][$matches[1]] = array();
     }
     $handles['hooks'][$matches[1]][] = $method->getName();
    }
   }
  }
  // fill up rest of information
  $handles['instance'] = NULL;
  $handles['name'] = $lib;
  // eliminate notices
  if (!isset($handles['filters'])) $handles['filters'] = array();
  if (!isset($handles['tags'])) $handles['tags'] = array();
  if (!isset($handles['hooks'])) $handles['hooks'] = array();
  // cache result
  file_put_contents($compiledFile, serialize($handles));
 }
 /**
  * Get (and create, if needed) library instance for given context
  * @param $library Library name (string)
  * @param $manager TemplateManager instance (for runtime libraries) or null
  * @param $compiler TemplateCompiler instance (for compile libraries) or null
  * @return Library instance
  */
 public function getLibrary($library, $manager, $compiler) {
  if (!isset($this->libraries[$library])) {
   // library is not loaded
   throw new TemplateRuntimeError(TemplateError::E_LIBRARY_NOT_LOADED, array($library));
  }
  $class = $library.'Library';
  if (!is_object($this->libraries[$library]['instance'])) {
   $createOptions = array();
   if (TemplateUtils::doesImplement($class, 'ITemplateRuntimeLibrary')) {
    $createOptions['manager'] = $manager;
   }
   if (TemplateUtils::doesImplement($class, 'ITemplateCompileLibrary')) {
    $createOptions['compiler'] = $compiler;
   }
   if (!TemplateUtils::doesImplement($class, 'ITemplateLibrary') || empty($createOptions)) {
    throw new TemplateRuntimeError(TemplateError::E_INVALID_LIBRARY, array($library));
   }
   $this->libraries[$library]['instance'] = new $class($createOptions);
  }
  return $this->libraries[$library]['instance'];
 }
 /**
  * Search for library with given handler.
  * @param $context Context name (string)
  * @param $type Handler type to find (string)
  * @param $subject Handler subject (name of tag, filter, etc.) to find (string)
  * @return Library name or boolean false if not found
  */
 public function findLibrary($context, $type, $subject) {
  // core libraries are always enabled
  if (!isset($this->enabledLibraries[$context])) {
   $this->enabledLibraries[$context] = array();
  }
  foreach (array_merge($this->enabledLibraries[$context], $this->coreLibraries) as $library) {
   if (isset($this->libraries[$library][$type][$subject])) {
    return $this->libraries[$library]['name'];
   }
  }
  return false;
 }
 /**
  * Return handler method name for given handler in given library.
  * @param $library Library name (string)
  * @param $type Handler type (string)
  * @param $subject Handler subject (string)
  * @return Library method name handling given subject or false if none
  */
 public function getCallback($library, $type, $subject) {
  if (!isset($this->libraries[$library][$type][$subject])) {
   // library's not loaded, or library hasn't got what we're looking for
   return false;
  }
  return $this->libraries[$library][$type][$subject];
 }
 /**
  * Find given type of plugin in plugin dirs
  * @param $type Plugin type
  * @param $name Plugin name
  * @param $settings Setting array
  * @return Plugin classname
  */
 public function findPlugin($type, $name, array $settings) {
  switch ($type) {
   case 'IO':
    $className = 'Template'.$name.'IO';
    $fileName = $name.'.io.php';
    $interface = 'ITemplateIO';
   break;
   case 'Library':
    $className = $name.'Library';
    $fileName = $name.'.library.php';
    $interface = 'ITemplateLibrary';
   break;
  }
  $dir = $this->findPluginFile($fileName, $settings);
  if (class_exists($className, false) && TemplateUtils::doesImplement($className, $interface)) {
   return array($className, $dir.$fileName);
  }
  return false;
 }
 /**
  * findPlugin subroutine
  */
 protected function findPluginFile($fileName, array $settings) {
  foreach ($settings['pluginsDirs'] as $dir) {
   if (file_exists($dir.$fileName)) {
    include_once $dir.$fileName;
    return $dir;
   }
  }
 }
 /**
  * Get all handlers for given hook
  * @param $context Context name
  * @param $hook Hook name
  * @param $compiler ITemplateCompiler
  * @return Array
  */
 public function getHooks($context, $hook, ITemplateCompiler $compiler) {
  // core libraries are always enabled
  if (!isset($this->enabledLibraries[$context])) {
   $this->enabledLibraries[$context] = array();
  }
  $foundHooks = array();
  foreach (array_merge($this->enabledLibraries[$context], $this->coreLibraries) as $library) {
   $libraryInstance = $this->getLibrary($library, NULL, $compiler);
   if (isset($this->libraries[$library]['hooks'][$hook])) {
    foreach ($this->libraries[$library]['hooks'][$hook] as $handler) {
     $foundHooks[$handler] = $libraryInstance;
    }
   }
  }
  return $foundHooks;
 }
}
