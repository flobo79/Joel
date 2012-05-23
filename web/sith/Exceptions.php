<?php
/**
 * Base class for exceptions.
 * @version $Id: Exceptions.php 715 2009-01-03 16:56:46Z piotrlegnica $
 * @license{New BSD License}
 * @author PiotrLegnica
 */
abstract class TemplateError extends Exception {
 // syntax errors
 /**
  * Unclosed tags
  */
 const E_UNCLOSED_TAGS = 100;
 /**
  * Unexpected template chunk
  */
 const E_UNEXPECTED = 101;
 /**
  * Unknown tag encountered
  */
 const E_UNKNOWN_TAG = 102;
 /**
  * Unknown filter encountered
  */
 const E_UNKNOWN_FILTER = 103;
 /**
  * Invalid tag encountered
  */
 const E_INVALID_TAG = 104;
 /**
  * Tag is misplaced
  */
 const E_MISPLACED_TAG = 105;
 /**
  * Invalid argument encountered
  */
 const E_INVALID_ARGUMENT = 106;
 /**
  * Too few arguments passed
  */
 const E_TOO_FEW_ARGUMENTS = 107;
 /**
  * Disallowed element (used also in runtime)
  */
 const E_DISALLOWED = 108;
 // runtime errors
 /**
  * Unknown I/O driver
  */
 const E_UNKNOWN_IO_DRIVER = 200;
 /**
  * Template doesn't exist
  */
 const E_TEMPLATE_NOT_FOUND = 201;
 /**
  * I/O error
  */
 const E_IO_FAILURE = 202;
 /**
  * Invalid setting value
  */
 const E_INVALID_SETTING = 203;
 /**
  * Unknown library
  */
 const E_UNKNOWN_LIBRARY = 204;
 /**
  * Library not loaded
  */
 const E_LIBRARY_NOT_LOADED = 205;
 /**
  * Invalid library
  */
 const E_INVALID_LIBRARY = 206;
 /**
  * Invalid template
  */
 const E_INVALID_TEMPLATE = 207;
 /**
  * Invalid data passed
  */
 const E_INVALID_DATA = 208;
 /**
  * Variable doesn't exist
  */
 const E_UNKNOWN_VARIABLE = 209;
 /**
  * Error messages
  */
 public static $messages = array(
  // syntax errors
  self::E_UNCLOSED_TAGS =>
   'Closing tags are missing for tags: %s',
  self::E_UNEXPECTED =>
   'Unexpected "%s" encountered, "%s" expected',
  self::E_UNKNOWN_TAG =>
   'Unknown tag "%s" encountered',
  self::E_UNKNOWN_FILTER =>
   'Unknown filter "%s" encountered',
  self::E_INVALID_TAG =>
   'Tag "%s" is invalid - %s',
  self::E_MISPLACED_TAG =>
   'Tag "%s" is misplaced - %s',
  self::E_INVALID_ARGUMENT =>
   'Argument "%s" for tag "%s" is invalid - %s',
  self::E_TOO_FEW_ARGUMENTS =>
   'Tag "%s" requires at least %d arguments, %d given',
  self::E_DISALLOWED =>
   'Element "%s" of type "%s" has been disallowed by security settings',
  // runtime errors
  self::E_UNKNOWN_IO_DRIVER =>
   'Unknown I/O driver "%s" used',
  self::E_TEMPLATE_NOT_FOUND =>
   'Template "%s" not found',
  self::E_IO_FAILURE =>
   'I/O failure - %s',
  self::E_INVALID_SETTING =>
   'Setting "%s" has an invalid value - %s',
  self::E_UNKNOWN_LIBRARY =>
   'Requested unknown library "%s"',
  self::E_LIBRARY_NOT_LOADED =>
   'Library "%s" hasn\'t been loaded',
  self::E_INVALID_LIBRARY =>
   'Library "%s" is invalid - %s',
  self::E_INVALID_TEMPLATE =>
   'Template "%s" is invalid - %s',
  self::E_INVALID_DATA =>
   'Integrity check failed - %s',
  self::E_UNKNOWN_VARIABLE =>
   'Variable "%s" used but not found in current context',
 );
 /**
  * Constructor
  * @param $message Error message
  * @param $code Error code
  */
 public function __construct($message, $code) {
  parent::__construct($message, $code);
 }
}
/**
 * Exception thrown when syntax error is found.
 * @version $Id: Exceptions.php 715 2009-01-03 16:56:46Z piotrlegnica $
 * @license{New BSD License}
 * @author PiotrLegnica
 */
class TemplateSyntaxError extends TemplateError {
 /**
  * ITemplateCompilerState implementation instance.
  */
 protected $state;
 /**
  * Constructor.
  * @param $state ITemplateCompilerState implementation instance
  * @param $code Error code
  * @param $param Array of additional parameters
  */
 public function __construct(ITemplateCompilerState $state, $code, array $param = array()) {
  switch ($code) {
   case self::E_UNCLOSED_TAGS:
    $message = sprintf(self::$messages[$code], $state->getOpenTags(true));
   break;
   default:
    if (!isset(self::$messages[$code])) {
     $message = 'Unknown error';
    } else {
     $message = vsprintf(self::$messages[$code], $param);
    }
   break;
  }
  parent::__construct($message, $code);
  $this->state = $state;
 }
 /**
  * Get state from exception.
  * @return ITemplateCompilerState implementation instance
  */
 public function getState() {
  return $this->state;
 }
}
/**
 * Exception thrown when runtime error occurs.
 * @since 0.3.1
 * @version $Id: Exceptions.php 715 2009-01-03 16:56:46Z piotrlegnica $
 * @license{New BSD License}
 * @author PiotrLegnica
 */
class TemplateRuntimeError extends TemplateError {
 /**
  * Constructor
  * @param $code Error code
  * @param $params Additional parameters
  */
 public function __construct($code, $params = array()) {
  if (!isset(self::$messages[$code])) {
   $message = 'Unknown error';
  } else {
   $message = vsprintf(self::$messages[$code], $params);
  }
  parent::__construct($message, $code);
 }
}
