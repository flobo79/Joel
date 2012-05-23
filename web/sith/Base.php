<?php
/**
 * Base type for templates.
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Base.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
abstract class TemplateBase {
 /**
  * Current context.
  */
 protected $ctx = array();
 /**
  * ITemplateManager implementation instance
  */
 protected $manager = NULL;
 /**
  * ITemplateLibraryLoader implementation instance
  */
 protected $loader = NULL;
 /**
  * Token template
  */
 protected $token = '';
 /**
  * Variable policy
  */
 protected $policy = 0;
 /**
  * Settings
  */
 protected $settings = array();
 /**
  * I18n provider
  */
 protected $i18n = NULL;
 /**
  * Constructor
  * @param $manager ITemplateManager implementation instance
  */
 public function __construct(ITemplateManager $manager) {
  $this->manager = $manager;
  $this->loader = $this->manager->getLibraryLoader();
 }
 /**
  * Run template and return result
  * @param $ctx Context
  * @return Result - string
  */
 public final function runTemplate(array $ctx) {
  $this->ctx = $ctx;
  $this->settings = $this->manager->getSettings();
  $this->policy = $this->settings['variableHandlingPolicy'];
  $this->token = str_replace('%', '%%', $this->settings['variableOpening']).
                 ' %s '.
                 str_replace('%', '%%', $this->settings['variableClosing']);
                 
  return $this->_block_main();
 }
 /**
  * Handle non-existant variable
  * @param $variable Variable notation
  */
 protected final function variableDoesNotExist($variable) {
  switch ($this->policy) {
   case TemplateManager::POLICY_FAIL_SILENTLY:
    return ''; // make it empty string and shut up
   break; // for clarity
   case TemplateManager::POLICY_DISPLAY_TOKEN:
    return sprintf($this->token, $variable);
    // make it raw token used in template
   break; // for clarity
   case TemplateManager::POLICY_RAISE_WARNING:
    trigger_error($variable.' variable used but doesn\'t exist in current context',
                  E_USER_WARNING); // raise warning and make it empty string
    return '';
   break; // for clarity
   case TemplateManager::POLICY_RAISE_ERROR:
    throw new TemplateRuntimeError(TemplateError::E_UNKNOWN_VARIABLE,
                                   array($variable));
    // raise runtime error
   break; // for clarity
  }
 }
 /**
  * Handle invalid variable
  * @param $variable Variable notation
  * @param $tokenContents Raw token
  * @param $tokenType Type of token
  * @param $message Additional message
  * @return String to display
  */
 protected final function variableIsInvalid($variable, $tokenContents, $tokenType, $message) {
  $message = 'Invalid variable "'.$variable.'" used in token "'.
             $this->settings[$tokenType.'Opening'].' '.$tokenContents.' '.
             $this->settings[$tokenType.'Closing'].'": '.$message;
  switch ($this->policy) {
   case TemplateManager::POLICY_FAIL_SILENTLY:
    return ''; // fail silently
   break; // for clarity
   case TemplateManager::POLICY_DISPLAY_TOKEN:
    return $message;
   break; // for clarity
   case TemplateManager::POLICY_RAISE_WARNING:
    trigger_error($message, E_USER_WARNING);
    return '';
   break; // for clarity
   case TemplateManager::POLICY_RAISE_ERROR:
    throw new TemplateRuntimeError(TemplateError::E_INVALID_DATA, array($message));
   break; // for clarity
  }
 }
 /**
  * Main block of template
  */
 protected abstract function _block_main();
}
