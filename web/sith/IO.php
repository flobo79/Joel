<?php
/**
 * Default I/O driver.
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: IO.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateDefaultIO implements ITemplateIO {
 /**
  * Templates' data.
  */
 protected $templates = array();
 /**
  * Settings
  */
 protected $settings;
 /**
  * Constructor
  * @param $settings Array of settings
  */
 public function __construct(array &$settings) {
  $this->settings = &$settings;
 }
 /**
  * Cache template data
  * @param $templateID Template ID
  */
 protected function createTemplateData($templateID) {
  $this->templates[$templateID] = array(
   'className' => 'Template'.TemplateUtils::sanitize($templateID),
   'templatePath' => $this->settings['templateDir'].$templateID,
  );
  $this->templates[$templateID]['compiledPath'] =
   $this->settings['compileDir'].'tpl.'.$this->templates[$templateID]['className'].
   '.compiled.php';
  $this->templates[$templateID]['metadataPath'] =
   $this->settings['compileDir'].'tpl.'.$this->templates[$templateID]['className'].
   '.meta';
 }
 /**
  * Does template need to be compiled?
  * @param $templateID String
  * @return Boolean
  */
 public function needCompiling($templateID) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  if (!class_exists($this->templates[$templateID]['className'], false)) {
   if (!file_exists($this->templates[$templateID]['templatePath'])) {
    throw new TemplateRuntimeError(TemplateError::E_TEMPLATE_NOT_FOUND, array($templateID));
   }
   if (!file_exists($this->templates[$templateID]['metadataPath'])) {
    // if metadata is missing, assume that template is broken/incomplete
    return true;
   }
   // if user choose never to recompile templates, don't make filemtime calls
   if ($this->settings['recompileMode'] == TemplateManager::RECOMPILE_NEVER &&
       !file_exists($this->templates[$templateID]['compiledPath'])) {
    return true;
   } elseif ($this->settings['recompileMode'] != TemplateManager::RECOMPILE_NEVER) {
    // if we are in RECOMPILE_ALWAYS mode, then don't bother checking for cache expiration
    if ($this->settings['recompileMode'] == TemplateManager::RECOMPILE_ALWAYS) {
     return true;
    } elseif (!file_exists($this->templates[$templateID]['compiledPath']) ||
              (filemtime($this->templates[$templateID]['compiledPath']) <
               filemtime($this->templates[$templateID]['templatePath']))) {
     return true;
    }
   }
  }
  return false;
 }
 /**
  * Include template class
  * @param $templateID String
  */
 public function includeTemplate($templateID) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  include_once $this->templates[$templateID]['compiledPath'];
  if (!class_exists($this->templates[$templateID]['className'], false)) {
   throw new TemplateRuntimeError(TemplateError::E_INVALID_TEMPLATE,
                                  array(
                                   $templateID,
                                   'Class '.$this->templates[$templateID]['className'].' not found'
                                  ));
  }
 }
 /**
  * Load and return template source
  * @param $templateID String
  * @return Array
  */
 public function loadTemplate($templateID) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  return file($this->templates[$templateID]['templatePath']);
 }
 /**
  * Load and return template metadata
  * @param $templateID String
  * @return Array
  */
 public function loadMetadata($templateID) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  $metadata = unserialize(file_get_contents($this->templates[$templateID]['metadataPath']));
  return $metadata;
 }
 /**
  * Save compiled template and its metadata
  * @param $templateID String
  * @param $code PHP code of template
  * @param $metadata Template metadata
  */
 public function saveTemplate($templateID, $code, array $metadata) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  if (!file_put_contents($this->templates[$templateID]['compiledPath'], $code)) {
   throw new TemplateRuntimeError(TemplateError::E_IO_FAILURE,
                                  array(
                                   'Cannot write to '.$this->templates[$templateID]['compiledPath']
                                  ));
  }
  //$metadata = TemplateUtils::strip(var_export($metadata, true));
  if (!file_put_contents($this->templates[$templateID]['metadataPath'], serialize($metadata))) {
   throw new TemplateRuntimeError(TemplateError::E_IO_FAILURE,
                                  array('Cannot write to '.$this->templates[$templateID]['metadataPath']));
  }
 }
 /**
  * Return template classname
  * @param $templateID String
  * @return String
  */
 public function getClassName($templateID) {
  if (!isset($this->templates[$templateID])) {
   $this->createTemplateData($templateID);
  }
  return $this->templates[$templateID]['className'];
 }
}
