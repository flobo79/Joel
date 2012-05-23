<?php
/**
 * Interface for template compilers.
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateCompiler {
 /**
  * Reset state
  */
 public function resetState();
 /**
  * @return ITemplateCompilerState
  */
 public function getState();
 /**
  * @return ITemplateManager
  */
 public function getManager();
 /**
  * @return ITemplateValidator
  */
 public function getValidator();
 /**
  * Compile template in current state
  */
 public function compile();
 /**
  * Process single line
  * @param $line String
  */
 public function processLine($line);
 /**
  * Process single token
  * @param $token Array
  */
 public function processToken(array $token);
 /**
  * Process single tag
  * @param $name String
  * @param $arguments Array
  */
 public function processTag($name, array $arguments);
 /**
  * Process single variable
  * @param $variable String
  * @return Code
  */
 public function processVariable($variable);
 /**
  * Process array of filters
  * @param $filters Array
  * @param $value String
  * @return Code
  */
 public function processFilters(array $filters, $value);
 /**
  * Transform filter chain string into array of filters
  * @param $filters String
  * @return Array
  */
 public function processFilterChain($filters);
 /**
  * Raise syntax error
  * @param $code Integer
  * @param $params Array
  */
 public function raiseSyntaxError($code, array $params = array());
}
/**
 * Interface for template managers
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateManager
 extends ArrayAccess
 {
 /**
  * @return Array
  */
 public function getSettings();
 /**
  * @param $settings String (filename) or array
  */
 public function setSettings($settings);
 /**
  * @return ITemplateCompiler
  */
 public function getCompiler();
 /**
  * @param $compiler ITemplateCompiler
  */
 public function setCompiler(ITemplateCompiler $compiler);
 /**
  * @return ITemplateLibraryLoader
  */
 public function getLibraryLoader();
 /**
  * @param $loader ITemplateLibraryLoader
  */
 public function setLibraryLoader(ITemplateLibraryLoader $loader);
 /**
  * @return ITemplateI18n
  */
 public function getI18nProvider();
 /**
  * @param $provider ITemplateI18n
  */
 public function setI18nProvider(ITemplateI18n $provider);
 /**
  * Add variable to shared context
  * @param $variable String
  * @param $value Mixed
  * @param $override Boolean
  */
 public function addVariable($variable, $value, $override = true);
 /**
  * Add variable by reference to shared context
  * @param $variable String
  * @param $value Mixed
  * @param $override Boolean
  */
 public function addVariableByRef($variable, &$value, $override = true);
 /**
  * Remove variable from shared context
  * @param $variable String
  */
 public function removeVariable($variable);
 /**
  * Add multiple variables to shared context
  * @param $variables Array
  */
 public function addVariables(array $variables);
 /**
  * Remove multiple variables from shared context
  * @param $variables Array
  */
 public function removeVariables(array $variables);
 /**
  * Get variable from shared context
  * @param $variable String
  * @return Mixed, NULL if variable doesn't exist in the context
  */
 public function getVariable($variable);
 /**
  * Is variable in shared context?
  * @param $variable String
  * @return Mixed
  */
 public function hasVariable($variable);
 /**
  * Parse template
  * @param $templateID String
  * @param $context Array
  * @return String
  */
 public function parse($templateID, array $context = array());
 /**
  * Parse string as template
  * @param $template String
  * @param $context Array
  * @return String
  */
 public function parseString($template, array $context = array());
 /**
  * Parse template and echo result
  * @param $templateID String
  * @param $context Array
  */
 public function display($templateID, array $context = array());
 /**
  * Parse string as template and echo result
  * @param $template String
  * @param $context Array
  */
 public function displayString($template, array $context = array());
 /**
  * Raise TemplateRuntimeError
  * @param $code Integer
  * @param $params Array
  */
 public function raiseRuntimeError($code, array $params = array());
 /**
  * Include template
  * @param $templateID Template ID
  * @param $createInstance Create instance?
  * @return Template instance or NULL
  */
 public function includeTemplate($templateID, $createInstance = true);
}
/**
 * Interface for template compiler state managers
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateCompilerState {
 /**
  * @return Array
  */
 public function getSource();
 /**
  * @param $source Array
  */
 public function setSource(array $source);
 /**
  * @param $line Integer
  * @return String
  */
 public function getLine($line = NULL);
 /**
  * @param $start Integer
  * @param $end Integer
  * @return Array
  */
 public function getLines($start, $end);
 /**
  * Advance to next line
  */
 public function nextLine();
 /**
  * @return Integer
  */
 public function getCurrentLine();
 /**
  * @param $openingTag String
  * @param $closingTag String
  */
 public function openTag($openingTag, $closingTag);
 /**
  * @param $openingTag String
  * @param $closingTag String
  */
 public function closeTag($openingTag, $closingTag);
 /**
  * @param $openingTag String
  * @param $closingTag String
  */
 public function verifyTag($openingTag, $closingTag);
 /**
  * @param $asString Boolean
  * @return Array
  */
 public function getOpenTags($asString = false);
 /**
  * @param $blockID String
  */
 public function enterBlock($blockID);
 /**
  * @param $blockID String
  */
 public function exitBlock($blockID = NULL);
 /**
  * @return Array
  */
 public function getOpenBlocks();
 /**
  * @return String
  */
 public function getCurrentBlock();
 /**
  * @param $code String
  * @param $blockID String
  */
 public function addCode($code, $blockID = NULL);
 /**
  * @param $code String
  * @param $prepend Boolean
  */
 public function addInitCode($code, $prepend = false);
 /**
  * @param $text String
  * @param $blockID String
  */
 public function addText($text, $blockID = NULL);
 /**
  * @param $blockID String
  * @return String
  */
 public function getCode($blockID);
 /**
  * @return String
  */
 public function getInitCode();
 /**
  * @return Array
  */
 public function getAllCodes();
 /**
  * @return Array
  */
 public function getSettings();
 /**
  * Proxy method, ensures that $this->variables is read-only.
  * @param $variable String
  * @return ArrayObject, $this->variables
  */
 public function __get($variable);
}
/**
 * Interface for I/O drivers
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateIO {
 /**
  * Does template need to be compiled?
  * @param $templateID String
  * @return Boolean
  */
 public function needCompiling($templateID);
 /**
  * Load and return template source
  * @param $templateID String
  * @return Array
  */
 public function loadTemplate($templateID);
 /**
  * Load and return template metadata
  * @param $templateID String
  * @return Array
  */
 public function loadMetadata($templateID);
 /**
  * Save compiled template and its metadata
  * @param $templateID String
  * @param $code String
  * @param $metadata Array
  */
 public function saveTemplate($templateID, $code, array $metadata);
 /**
  * Get classname of template
  * @param $templateID String
  * @return String
  */
 public function getClassName($templateID);
 /**
  * Include template class
  * @param $templateID String
  */
 public function includeTemplate($templateID);
}
/**
 * Common type for libraries.
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateLibrary {
 /**
  * Method called when Reflection is unavailable
  * @return Array
  */
 public static function getHandlers();
}
/**
 * Type for compile-time libraries
 * @since 0.7.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateCompileLibrary extends ITemplateLibrary {}
/**
 * Type for runtime libraries
 * @since 0.7.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateRuntimeLibrary extends ITemplateLibrary {}
/**
 * Interface for library loaders
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateLibraryLoader {
 /**
  * Find plugin in given plugin directories
  * @param $type Enum: Library or IO
  * @param $name String
  * @param $settings Array
  */
 public function findPlugin($type, $name, array $settings);
 /**
  * Get name of handler
  * @param $library String
  * @param $type String
  * @param $subject String
  */
 public function getCallback($library, $type, $subject);
 /**
  * Search for library with given handler
  * @param $context String)
  * @param $type String)
  * @param $subject String)
  * @return Library name or boolean false
  */
 public function findLibrary($context, $type, $subject);
 /**
  * Load and parse library.
  * @param $lib String
  * @param $settings Array
  */
 public function loadLibrary($lib, array $settings);
 /**
  * Enable library in given context
  * @param $context String
  * @param $lib String
  */
 public function enableLibrary($context, $lib);
 /**
  * Get (and create, if needed) library instance for given context
  * @param $library String
  * @param $manager ITemplateManager implementation instance or NULL
  * @param $compiler ITemplateCompiler implementation instance or NULL
  * @return Library instance
  */
 public function getLibrary($library, $manager, $compiler);
 /**
  * Get all handlers for given hook
  * @param $context String
  * @param $hook String
  * @param $compiler ITemplateCompiler
  * @return Array
  */
 public function getHooks($context, $hook, ITemplateCompiler $compiler);
}
/**
 * Interface for alternative TemplateValidator implementations
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateValidator {
 /**
  * @param $argument String
  * @return Boolean
  */
 public function isQuotedString($argument);
 /**
  * @param $argument String
  * @param $float Boolean
  * @return Boolean
  */
 public function isNumber($argument, $float = false);
 /**
  * @param $argument String
  * @return Boolean
  */
 public function isVariable($argument);
}
/**
 * Interface for i18n providers.
 * @since 0.9.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Interfaces.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
interface ITemplateI18n {
 /**
  * Return translated and formatted string (variable number of args)
  * @param $str String to translate
  */
 public function tr($str);
 /**
  * Return translated and formatted string in correct form (var. number of args)
  * @param $singular String to translate (singular form)
  * @param $plural String to translate (plural form)
  * @param $count Integer used to determine correct form
  */
 public function ntr($singular, $plural, $count);
}
