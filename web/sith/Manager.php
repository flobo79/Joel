<?php
/**
 * Default implementation of template manager.
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Manager.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateManager implements ITemplateManager {
 /**
  * Current settings.
  */
 protected $settings = array(
  // [Directories]
  'templateDir' => './templates/',
  'compileDir' => './templates_c/',
  'pluginsDirs' => array(),
  'useDefaultPluginDir' => true,
  // [Compilation]
  'recompileMode' => self::RECOMPILE_IF_CHANGED,
  'variableHandlingPolicy' => self::POLICY_FAIL_SILENTLY,
  // [Delimiters]
  'tagOpening' => '{%',
  'tagClosing' => '%}',
  'variableOpening' => '{{',
  'variableClosing' => '}}',
  'commentOpening' => '{#',
  'commentClosing' => '#}',
  'variablePrefix' => '',
  // [Classes]
  'compilerClass' => 'TemplateCompiler',
  'libraryLoaderClass' => 'TemplateLibraryLoader',
  'i18nClass' => '',
  // [Security]
  'enableSecurity' => false,
  'securityPolicy' => self::SECURITY_WHITE_LIST,
  'securityFilters' => array(
   'add', 'addslashes', 'capfirst', 'cut', 'date', 'default', 'divisibleby',
   'escape', 'filesizeformat', 'fix_ampersands', 'join', 'length', 'length_is',
   'linebreaks', 'linebreaksbr', 'ljust', 'lower', 'make_list', 'pluralize',
   'random', 'removetags', 'rjust', 'slugify', 'upper', 'urlencode',
   'wordcount', 'wordwrap',
  ),
  'securityTags' => array(
   'block', 'endblock', 'comment', 'endcomment', 'cycle', 'debug', 'extends',
   'filter', 'endfilter', 'firstof', 'for', 'endfor', 'if', 'endif', 'else',
   'elseif', 'include', 'now', 'templatetag', 'widthratio', 'with', 'endwith',
   'putblock', 'call', 'load',
  ),
  'securityFunctions' => array(),
  'securityAutoEscape' => false,
 );
 /**
  * ITemplateCompiler instance
  */
 protected $compiler;
 /**
  * ITemplateLibraryLoader instance
  */
 protected $loader;
 /**
  * ITemplateI18n instance
  */
 protected $i18n;
 /**
  * Shared context (array)
  */
 protected $sharedContext = array();
 /**
  * Internal cache of I/O drivers
  */
 protected $cacheIODrivers = array();
 /**
  * Internal cache of templates
  */
 protected $cacheTemplates = array();
 /**
  * Never recompile
  */
 const RECOMPILE_NEVER = -1;
 /**
  * Recompile only if template has been changed
  */
 const RECOMPILE_IF_CHANGED = 0;
 /**
  * Always recompile
  */
 const RECOMPILE_ALWAYS = 1;
 /**
  * Fail silently if variable doesn't exist
  */
 const POLICY_FAIL_SILENTLY = 0;
 /**
  * Display token if variable doesn't exist
  */
 const POLICY_DISPLAY_TOKEN = 1;
 /**
  * Raise E_USER_WARNING if variable doesn't exist
  */
 const POLICY_RAISE_WARNING = 2;
 /**
  * Raise TemplateRuntimeError if variable doesn't exist
  */
 const POLICY_RAISE_ERROR = 3;
 /**
  * Treat security lists as whitelists
  */
 const SECURITY_WHITE_LIST = 0;
 /**
  * Treat security lists as blacklists
  */
 const SECURITY_BLACK_LIST = 1;

 /**
  * Constructor
  */
 public function __construct() {
  // set default plugin dir
  $this->setSettings(NULL);
 }
 /**
  * @copydoc TemplateManager::unloadInternalCache
  * @deprecated Since 0.9-dev, use unloadInternalCache instead
  */
 public function unloadCache() {
  trigger_error('Method "unloadCache" is deprecated. Use "unloadInternalCache" instead.',
                E_USER_WARNING);
  $this->unloadInternalCache();
 }
 /**
  * Unloads internal cache (templates and I/O drivers). Can be used to free RAM.
  */
 public function unloadInternalCache() {
  unset($this->cacheIODrivers);
  unset($this->cacheTemplates);
  $this->cacheIODrivers = array();
  $this->cacheTemplates = array();
 }
 /**
  * Get current settings
  * @return Array of settings
  */
 public function getSettings() {
  return $this->settings;
 }
 /**
  * @param $settings String (filename) or array
  */
 public function setSettings($settings) {
  if (is_array($settings)) {
   // array settings given
   $this->settings = array_merge($this->settings, $settings);
  } elseif (is_string($settings)) {
   // INI filename given
   $this->settings = array_merge($this->settings, parse_ini_file($settings));
  }
  if ($this->settings['useDefaultPluginDir'] && !in_array(SITHTEMPLATE_DIR.'plugins/', $this->settings['pluginsDirs'])) {
   $this->settings['pluginsDirs'][] = SITHTEMPLATE_DIR.'plugins/';
  }
 }
 /**
  * Return current template compiler. Note - it will create compiler instance
  * (and load Compiler.php) if it doesn't exist yet
  * @return ITemplateCompiler implementation instance
  */
 public function getCompiler() {
  if (!is_object($this->compiler) || !TemplateUtils::doesImplement($this->compiler, 'ITemplateCompiler')) {
   $this->compiler = new $this->settings['compilerClass']($this);
  }
  return $this->compiler;
 }
 /**
  * Set new compiler instance, you can use it to replace compiler
  * @param $compiler ITemplateCompiler implementation instance
  */
 public function setCompiler(ITemplateCompiler $compiler) {
  $this->compiler = $compiler;
 }
 /**
  * @return ITemplateLibraryLoader
  */
 public function getLibraryLoader() {
  if (!is_object($this->loader) || !TemplateUtils::doesImplement($this->loader, 'ITemplateLibraryLoader')) {
   $this->loader = new $this->settings['libraryLoaderClass']();
  }
  return $this->loader;
 }
 /**
  * @param $loader ITemplateLibraryLoader
  */
 public function setLibraryLoader(ITemplateLibraryLoader $loader) {
  $this->loader = $loader;
 }
 /**
  * @return ITemplateI18n
  */
 public function getI18nProvider() {
  if (!is_object($this->i18n) || !TemplateUtils::doesImplement($this->i18n, 'ITemplateI18n')) {
   if (empty($this->settings['i18nClass'])) {
    $this->raiseRuntimeError(TemplateError::E_INVALID_DATA, array('I18n used but not configured'));
   }
   $this->i18n = new $this->settings['i18nClass']();
  }
  return $this->i18n;
 }
 /**
  * @param $provider ITemplateI18n
  */
 public function setI18nProvider(ITemplateI18n $provider) {
  $this->i18n = $provider;
 }
 /**
  * Add or change (if $override = false) variable in shared context
  * @param $variable String
  * @param $value Mixed
  * @param $override Boolean
  */
 public function addVariable($variable, $value, $override = true) {
  if ($variable == 'internal') { // reserved
   trigger_error('Variable "internal" is reserved.', E_USER_WARNING);
   return; // reserved
  }
  if (!$override && $this->hasVariable($variable)) {
   trigger_error('Variable "'.$variable.'" already exists in the context.', E_USER_WARNING);
   return;
  }
  $this->sharedContext[$variable] = $value;
 }
 /**
  * Add variable by reference to shared context
  * @param $variable String
  * @param $value Mixed
  * @param $override Boolean
  */
 public function addVariableByRef($variable, &$value, $override = true) {
  if ($variable == 'internal') { // reserved
   trigger_error('Variable "internal" is reserved.', E_USER_WARNING);
   return; // reserved
  }
  if (!$override && $this->hasVariable($variable)) {
   trigger_error('Variable "'.$variable.'" already exists in the context.', E_USER_WARNING);
   return;
  }
  $this->sharedContext[$variable] = &$value;
 }
 /**
  * Remove variable from shared context
  * @param $variable String
  */
 public function removeVariable($variable) {
  if ($variable == 'internal') { // reserved
   trigger_error('Variable "internal" is reserved and cannot be removed.',
                 E_USER_WARNING);
   return;
  }
  unset($this->sharedContext[$variable]);
 }
 /**
  * Add multiple variables to shared context
  * @param $variables Array
  */
 public function addVariables(array $variables) {
  if (isset($variables['internal'])) {
   unset($variables['internal']);
   trigger_error('Variable "internal" is reserved.', E_USER_WARNING);
  }
  $this->sharedContext = array_merge($this->sharedContext, $variables);
 }
 /**
  * Remove multiple variables from shared context
  * @param $variables Array
  */
 public function removeVariables(array $variables) {
  foreach ($variables as $variable) {
   $this->removeVariable($variable);
  }
 }
 /**
  * Get variable from shared context
  * @param $variable String
  * @return Mixed, NULL if variable doesn't exist in the context
  */
 public function getVariable($variable) {
  return ($this->hasVariable($variable) ? $this->sharedContext[$variable] : NULL);
 }
 /**
  * Is variable in shared context?
  * @param $variable String
  * @return Boolean
  */
 public function hasVariable($variable) {
  return isset($this->sharedContext[$variable]);
 }
 /**
  * Parse template
  * @param $templateID String
  * @param $context Array
  * @return String
  */
 public function parse($templateID, array $context = array()) {
  if (isset($context['internal'])) {
   unset($context['internal']);
   trigger_error('Variable "internal" is reserved and has been removed from your context.',
                 E_USER_WARNING);
  }
  // templateID = [ioDriver:]templateID
  // templateID is unique ID of template (eg. filename)
  return $this->includeTemplate($templateID)->runTemplate(array_merge($this->sharedContext, $context));
 }
 /**
  * Include template into current namespace
  * @param $templateID Template ID
  * @param $createInstance Create instance of template?
  * @return Template instance, if any
  */
 public function includeTemplate($templateID, $createInstance = true) {
  if (!isset($this->cacheTemplates[$templateID])) {
   if (strpos($templateID, ':') !== false) {
    list($driverName, $templateID) = TemplateUtils::split(':', $templateID);
    if (!isset($this->cacheIODrivers[$driverName])) {
     if (($driverClassName = $this->getLibraryLoader()->findPlugin(
                                                         'IO', $driverName,
                                                         $this->getSettings()
                                                        )) === false) {
      $this->raiseRuntimeError(TemplateError::E_UNKNOWN_IO_DRIVER, array($driverName));
     }
     $driverClassName = $driverClassName[0];
     $this->cacheIODrivers[$driverName] = new $driverClassName($this->settings);
    }
   } else {
    $driverName = 'default';
    if (!isset($this->cacheIODrivers[$driverName])) {
     $this->cacheIODrivers['default'] = new TemplateDefaultIO($this->settings);
    }
   }
   $className = $this->cacheIODrivers[$driverName]->getClassName($templateID);
   // load CoreFilter if necessary
   $this->getLibraryLoader()->loadLibrary('CoreFilter', $this->getSettings());
   if ($this->cacheIODrivers[$driverName]->needCompiling($templateID)) {
    // if template needs compiling, reset state, set source and run compiler
    $c = $this->getCompiler();
    $c->resetState();
    $state = $c->getState();
    $state->setSource($this->cacheIODrivers[$driverName]->loadTemplate($templateID));
    $state->variables->className = $className;
    $save = $c->compile();
    $this->cacheIODrivers[$driverName]->saveTemplate($templateID, $save['code'], $save['metadata']);
   }
   /**
    * This template is extending another, so we need to load parent before
    * actually including template. Fortunately, there can be only one parent :)
    */
   $metadata = $this->cacheIODrivers[$driverName]->loadMetadata($templateID);
   if (isset($metadata['parentTemplate'])) {
    $this->includeTemplate($metadata['parentTemplate'], false); // don't create unnecessary instances
   }
   // include template and return classname
   $this->cacheIODrivers[$driverName]->includeTemplate($templateID);
   if ($createInstance) {
    $this->cacheTemplates[$templateID] = new $className($this, $this->cacheIODrivers[$driverName]);
   } else {
    $this->cacheTemplates[$templateID] = NULL;
   }
  }
  return $this->cacheTemplates[$templateID];
 }
 /**
  * Parse string as template
  * @param $template String
  * @param $context Array
  * @return String
  */
 public function parseString($template, array $context = array()) {
  if (isset($context['internal'])) {
   unset($context['internal']);
   trigger_error('Variable "internal" is reserved and has been removed from your context.',
                 E_USER_WARNING);
  }
  // when compiling a string, recompileMode will be always NEVER
  // because when string changes, hash of it changes as well
  $templateID = md5($template);
  // create driver
  $settings = array_merge($this->getSettings(), array(
   'recompileMode' => self::RECOMPILE_NEVER
  ));
  $driver = new TemplateDefaultIO($settings);
  $className = $driver->getClassName($templateID);
  // load CoreFilter if necessary
  $this->getLibraryLoader()->loadLibrary('CoreFilter', $this->getSettings());
  if (!file_exists($this->settings['compileDir'].'tpl.'.$templateID.'.compiled.php')) {
   // if template needs compiling, reset state, set source and run compiler
   $c = $this->getCompiler();
   $c->resetState();
   $state = $c->getState();
   $state->setSource(array($template));
   $state->variables->className = $className;
   $save = $c->compile();
   $driver->saveTemplate($templateID, $save['code'], $save['metadata']);
  }
  // handle metadata
  $metadata = $driver->loadMetadata($templateID);
  if (isset($metadata['parentTemplate'])) {
   $this->includeTemplate($metadata['parentTemplate'], false);
  }
  // include template and return classname
  $driver->includeTemplate($templateID);
  $template = new $className($this);
  return $template->runTemplate(array_merge($this->sharedContext, $context));
 }
 /**
  * Parse template and echo result
  * @param $templateID String
  * @param $context Array
  */
 public function display($templateID, array $context = array()) {
  echo $this->parse($templateID, $context);
 }
 /**
  * Parse string as template and echo result
  * @param $template String
  * @param $context Array
  */
 public function displayString($template, array $context = array()) {
  echo $this->parseString($template, $context);
  }
 /**
  * Raise TemplateRuntimeError
  * @param $code Integer
  * @param $params Array
  */
 public function raiseRuntimeError($code, array $params = array()) {
  throw new TemplateRuntimeError($code, $params);
 }
 /**
  * ArrayAccess implementation.
  * Is variable in shared context?
  * @param $variable String
  * @return Boolean
  */
 public function offsetExists($variable) {
  return $this->hasVariable($variable);
 }
 /**
  * ArrayAccess implementation.
  * Return variable from shared context
  * @param $variable String
  * @return Mixed, NULL if variable doesn't exist in the context
  */
 public function offsetGet($variable) {
  return $this->getVariable($variable);
 }
 /**
  * ArrayAccess implementation.
  * Add or change variable in shared context.
  * Note that this method always use $override = true.
  * @param $variable String
  * @param $value Mixed
  */
 public function offsetSet($variable, $value) {
  $this->addVariable($variable, $value, true);
 }
 /**
  * ArrayAccess implementation.
  * Remove variable from shared context.
  * @param $variable String
  */
 public function offsetUnset($variable) {
  $this->removeVariable($variable);
 }
}
