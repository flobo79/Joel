<?php
/**
 * Default implementation of template compiler.
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Compiler.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateCompiler implements ITemplateCompiler {
 /**
  * ITemplateCompilerState instance.
  */
 protected $state;
 /**
  * ITemplateManager implementation instance.
  */
 protected $manager;
 /**
  * ITemplateLibraryLoader implementation instance.
  */
 protected $loader;
 /**
  * Settings array
  */
 protected $settings;
 /**
  * TemplateValidator instance.
  */
 protected $validator;
 
 /**
  * Constructor
  */
 public function __construct(ITemplateManager $manager) {
  $this->manager = $manager;
  $this->validator = new TemplateValidator;
 }
 /**
  * Reset state
  */
 public function resetState() {
  $this->settings = $this->manager->getSettings();
  $this->loader = $this->manager->getLibraryLoader();
  $this->state = new TemplateCompilerState($this->settings);
 }
 /**
  * Get current state.
  * @return TemplateCompilerState
  */
 public function getState() {
  return $this->state;
 }
 /**
  * Get current manager.
  * @return ITemplateManager implementation
  */
 public function getManager() {
  return $this->manager;
 }
 /**
  * Return ITemplateValidator implementation instance
  * @return ITemplateValidator implementation
  */
 public function getValidator() {
  return $this->validator;
 }
 /**
  * Parse current template.
  */
 public function compile() {
  // load CoreTag library
  $this->loader->loadLibrary('CoreTag', $this->settings);
  // load CoreHook library
  $this->loader->loadLibrary('CoreHook', $this->settings);
  // enter main block
  $this->state->enterBlock('block:main');
  // iterate through source lines
  // and process them
  while (($line = $this->state->getLine()) !== NULL) {
   $this->processLine($line);
   $this->state->nextLine();
  }
  if (count($this->state->getOpenTags()) > 0) {
   // there are still unclosed tags
   $this->raiseSyntaxError(TemplateError::E_UNCLOSED_TAGS);
  }
  $this->state->exitBlock('block:main');
  // generate class code
  $code = '<?php class '.$this->state->variables->className.' extends ';
  $metadata = array();
  // is template is extending another template?
  $hasParent = isset($this->state->variables->parentTemplate) && !empty($this->state->variables->parentTemplate);
  if ($hasParent) {
   $parent = $this->state->variables->parentTemplate;
   // we need to construct classname
   if (strpos($parent, ':') !== false) {
    list($driverName, $templateID) = TemplateUtils::split(':', $parent);
    if (($driverClassName = $this->loader->findPlugin('IO', $driverName,
                                                      $this->settings)) === false) {
     $this->manager->raiseRuntimeError(TemplateError::E_UNKNOWN_IO_DRIVER, array($driverName));
    }
    $driverClassName = $driverClassName[0];
    $driver = new $driverClassName($this->settings);
   } else {
    $templateID = $parent;
    $driver = new TemplateDefaultIO($this->settings);
   }
   $code .= $driver->getClassName($templateID);
   unset($driver);
   $metadata['parentTemplate'] = $parent;
  } else {
   $code .= 'TemplateBase';
  }
  $codes = $this->state->getAllCodes();
  $code .= '{';

  // execute init hooks and create init code
  $this->state->addInitCode('public function __construct($m){parent::__construct($m);', true);
  
  $this->runHooks('compile:commonInitHook');
  $this->runHooks('compile:commonMetadataHook', array(&$metadata));

  $libraries = $this->state->variables->metaVars->libraries;
  foreach ($libraries as &$library) {
   $libraryCode = '';
   if (!in_array($library, $this->loader->coreLibraries)) {
    // don't load core libraries in templates
    $libraryCode .= '$this->loader->loadLibrary(\'';
    $libraryCode .= TemplateUtils::escape($library);
    $libraryCode .= '\',$this->manager->getSettings());';
   }
   $libraryCode .= '$this->_lib_'.$library.'=$this->loader->getLibrary(\'';
   $libraryCode .= TemplateUtils::escape($library);
   $libraryCode .= '\',$this->manager,NULL);';
   
   $this->runHooks('compile:postGenLibraryCode', array($library, &$libraryCode));
   $library = $libraryCode;
  }
  
  $this->runHooks('compile:preAppendLibraryCode', array(&$libraries));
  $this->state->addInitCode(implode('', $libraries));
  
  if ($this->state->variables->metaVars->usingI18n) {
   $this->state->addInitCode('$this->i18n=$this->manager->getI18nProvider();');
  }
  
  $this->state->addInitCode('}');
  $code .= $this->state->getInitCode();
  
  // add blocks to class
  foreach ($codes as $block => $blockCode) {
   $blockFinalCode = '';
   if ($block == 'block:main') {
    // _block_main is always protected final function
    // it can't be added to template extending another template
    if ($hasParent) {
     continue;
    }
    $blockFinalCode .= 'protected final ';
   } else {
    $blockFinalCode .= 'protected ';
   }
   
   list($blockType, $blockName) = TemplateUtils::split(':', $block);

   $this->runHooks('compile:preAssembleBlockCode', array(&$blockType, &$blockName, &$blockCode));
   $blockFinalCode .= 'function _'.$blockType.'_'.$blockName.'(){'.$blockCode.'}';
   $this->runHooks('compile:postAssembleBlockCode', array(&$blockFinalCode));
   $code .= $blockFinalCode;
  }
  $code .= '}';
  // done
  return array('code' => $code, 'metadata' => $metadata);
 }
 /**
  * Process single line of template - tokenize it and call
  * TemplateCompiler::processTag for every token.
  * @param $line Template line to process
  */
 public function processLine($line) {
  /**
   * Regular expression used to tokenize template
   */
  $tokenRe = '~'.
   // tag
   '('.preg_quote($this->settings['tagOpening'], '~').'.*?'.
       preg_quote($this->settings['tagClosing'], '~').')|'.
   // variable
   '('.preg_quote($this->settings['variableOpening'], '~').'.*?'.
       preg_quote($this->settings['variableClosing'], '~').')|'.
   // comment
   '('.preg_quote($this->settings['commentOpening'], '~').'.*?'.
       preg_quote($this->settings['commentClosing'], '~').')~u';
  // tokenize
  $tokens = preg_split($tokenRe, $line, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
  // now parse each token
  foreach ($tokens as &$token) {
   $type = 'text';
   $contents = $token;
   if (substr($contents, 0, $this->state->variables->delimiterLengths->tagOpening) ==
       $this->settings['tagOpening']) {
    // it's a tag
    $type = 'tag';
    $contents = trim(substr($contents, $this->state->variables->delimiterLengths->tagOpening,
                                     -($this->state->variables->delimiterLengths->tagClosing)));
   } elseif (substr($contents, 0, $this->state->variables->delimiterLengths->variableOpening) ==
             $this->settings['variableOpening']) {
    // it's a variable
    $type = 'variable';
    $contents = trim(substr($contents, $this->state->variables->delimiterLengths->variableOpening,
                                     -($this->state->variables->delimiterLengths->variableClosing)));
   } elseif (substr($contents, 0, $this->state->variables->delimiterLengths->commentOpening) ==
             $this->settings['commentOpening']) {
    // it's a comment
    $type = 'comment';
    $contents = '';
   }
   // process token
   unset($token);
   $this->processToken(array('type' => $type, 'contents' => $contents));
  }
 }
 /**
  * Process single token - check its type (tag, variable, comment, text)
  * and call TemplateCompiler::processTag, TemplateCompiler::processVariable /
  * TemplateCompiler::processFilters, ignore it or add text to current state
  * respectively.
  * @param $token Token to process
  */
 public function processToken(array $token) {
  $this->runHooks('processToken:preTypeCheck', array(&$token));

  switch ($token['type']) {
   case 'tag':
    // we're processing a tag
    $arguments = preg_split('/(\".*?\")|\s+/', $token['contents'], -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $name = array_shift($arguments);
    
    $this->runHooks('processToken:preProcessTag', array(&$name, &$arguments));
    $this->processTag($name, $arguments);
   break;
   case 'variable':
    // we're processing a variable
    list($variable, $filters) = TemplateUtils::split('|', $token['contents']);

    $this->runHooks('processToken:preProcessVariable', array(&$variable, &$filters));
    $variable = $this->processVariable($variable);

    $this->runHooks('processToken:preProcessFilters', array(&$variable, &$filters));
    /**
     * simple explode wasn't good, because it can't correctly handle
     * strings like f1|f2:"|"|f3 (expected: f1, f2:"|", f3, explode
     * produced f1, f2:", ", f3.
     * therefore, custom letter-by-letter parsing subroutine
     * which is actually result of fight with regular expressions
     * to match filter strings like above :)
     * looks very C-ish, though
     */
    // create array from chain
    $chain = $this->processFilterChain($filters);

    $this->runHooks('processToken:postProcessFilterChain', array(&$chain));
    // create code
    $filteredVariable = $this->processFilters($chain, $variable['variableCode']);
    
    $this->runHooks('processToken:postProcessFilters', array(&$filteredVariable));

    // add variable checking code chunks
    $this->state->addCode($variable['variableCheck']);
    $this->state->addCode($filteredVariable['checkCode']);
    // and variable itself
    $this->state->addCode('$b.='.$filteredVariable['variableCode'].';');
   break;
   case 'text':
    // we're processing plain text
    // just add it as-is
    $this->runHooks('processToken:preAddText', array(&$token['contents']));
    $this->state->addText($token['contents']);
   break;
  }
 }
 /**
  * Properly split filter chain into array. processToken subroutine.
  * @param $filters Filter chain
  * @return Array
  */
 public function processFilterChain($filters) {
  // root state = 0
  // in-string state = 1
  $state = 0;
  // already split filters
  $splitFilters = array();
  // current filter
  $filter = '';
  for ($i = 0, $j = strlen($filters); $i < $j; $i++) {
   switch ($state) {
    // root state
    case 0:
     if ($filters[$i] == '"') {
      // enter in-string state without discarding "
      $filter .= $filters[$i];
      $state = 1;
     } elseif ($filters[$i] == '|') {
      // save filter and discard |
      $splitFilters[] = $filter;
      $filter = '';
     } else {
      // other chars are simply put back to filter string
      $filter .= $filters[$i];
     }
    break;
    // in-string state
    case 1:
     if ($filters[$i] == '"') {
      // exit in-string state and reenter root state, without discarding "
      $filter .= $filters[$i];
      $state = 0;
     } elseif ($filters[$i] == '\\' && $filters[$i+1] == '"') {
      // when " is escaped as \", treat it like literal and don't exit in-string state
      $filter .= $filters[$i+1];
      $i++; // skip one char
     } else {
      // just put back
      $filter .= $filters[$i];
     }
    break;
   }
  }
  // after exiting loop, there could be still one filter in buffer, save it
  if (!empty($filter)) {
   $splitFilters[] = $filter;
  }
  // and return them
  return $splitFilters;
 }
 /**
  * Process single tag - find proper library callback and run it
  * @param $name Tag name
  * @param $arguments Tag arguments
  */
 public function processTag($name, array $arguments) {
  // first, let's check if tag is allowed
  if (!TemplateUtils::isAllowed($this->settings, $name, 'securityTags')) {
   $this->raiseSyntaxError(TemplateError::E_DISALLOWED, array($name, 'tag'));
  }
  switch ($name) {
   // handle builtins
   case 'load':
    if (count($arguments) < 1) {
     $this->raiseSyntaxError(TemplateError::E_TOO_FEW_ARGUMENTS,
                             array($name, 1, count($arguments)));
    }
    if (!ctype_alnum($arguments[0])) {
     $this->raiseSyntaxError(TemplateError::E_INVALID_ARGUMENT,
                             array($arguments[0], $name, 'expected non-quoted string'));
    }
    // libraries are loaded once and once only
    // compiler additionally enables them for given context
    // so explicit {% load %} is required in any template
    // that want use that library
    $this->loader->loadLibrary($arguments[0], $this->settings);
    $this->loader->enableLibrary($this->state->variables->className, $arguments[0]);
    $this->state->variables->metaVars->libraries[] = $arguments[0];
   break;
   // handle plugins
   default:
    $className = $this->state->variables->className;
    // find library that handles encountered tag
    // if not found, raise syntax error
    if (($libName = $this->loader->findLibrary($className,
                                               'tags', $name)) === false) {
     $this->raiseSyntaxError(TemplateError::E_UNKNOWN_TAG, array($name));
    }
    // get instance of found library
    $library = $this->loader->getLibrary($libName, NULL, $this);
    // get callback for encountered tag
    $callback = $this->loader->getCallback($libName, 'tags', $name);
    // run tag handler
    $library->$callback($name, $arguments);
   break;
  }
 }
 /**
  * Transform variable notation into PHP code
  * @param $variable Variable notation to transform
  * @return PHP code for getting variable from context
  */
 public function processVariable($variable) {
  // generate variable access code
  // processVariableChunk part
  $this->runHooks('processVariable:preProcessVariableChunk', array(&$variable));
  $chunks = $this->processVariableChunk($variable);
  $this->runHooks('processVariable:postProcessVariableChunk', array(&$chunks));

  // processVariableChunks part
  $code = $this->processVariableChunks($chunks);
  $this->runHooks('processVariable:postProcessVariableChunks', array(&$code));

  // generate variable check code
  $check = 'if($this->policy&&!isset('.$code.')){'.
           $code.'=$this->variableDoesNotExist(\''.
           TemplateUtils::escape($variable).'\');}';
  $this->runHooks('processVariable:postCheckCode', array(&$check));

  return array(
   'variableCode' => '@'.$code,
   'rawVariableCode' => $code,
   'variableCheck' => $check,
  );
 }
 /**
  * Variable transforming sub-routine - recursive splitting
  * @param $variable Variable chunk to parse
  * @return Variable chunks
  */
 protected function processVariableChunk($variable) {
  $chunks = preg_split('/(\[.*?\]|\.|\-\>)/s', $variable, -1,
                      PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
  $prefixLen = strlen($this->settings['variablePrefix']);
  if ($prefixLen > 0) {
   // if prefix is set, then variable string always must start with it
   // even [foo] becomes <prefix>[<prefix>foo]
   if (($prefix = substr($chunks[0], 0, $prefixLen)) != $this->settings['variablePrefix']) {
    $this->raiseSyntaxError(TemplateError::E_UNEXPECTED, array(
     $prefix, $this->settings['variablePrefix']
    ));
   }
   $chunks[0] = (string)substr($chunks[0], $prefixLen);
  }
  foreach ($chunks as &$chunk) {
   if (substr($chunk, 0, 1) == '[') {
    // it's a variable-in-variable
    // it's a recursive call, so no prefix checking inside this loop
    $chunk = $this->processVariableChunk(substr($chunk, 1, -1));
   }
  }
  return array_filter($chunks, array($this, 'processVariableChunkNonEmpty'));
 }
 /**
  * processVariableChunk subroutine
  */
 protected function processVariableChunkNonEmpty($x) {
  return ($x !== ''); // don't even try to touch 0's
 }
 /**
  * Variable transforming sub-routine - recursive code generation
  * @param $chunks Chunks array
  * @return PHP code for variable
  */
 protected function processVariableChunks(array $chunks) {
  // first chunk is always key of an array (context)
  $next = 'array';
  $code = '$this->ctx';
  /**
   * Function and method calls has been moved
   * to {% call %} core tag, to avoid
   * long, hard, complex and weird regular
   * expressions in here
   */
  while (($chunk = array_shift($chunks)) !== NULL) {
   switch ($next) {
    case 'array':
     // this chunk is key of an array
     $code .= '[';
     if (is_array($chunk)) {
      // it's variable-in-variable
      $code .= $this->processVariableChunks($chunk);
     } elseif (ctype_digit($chunk)) {
      // it's number
      $code .= $chunk;
     } else {
      // it's string
      $code .= '\''.TemplateUtils::escape($chunk).'\'';
     }
     $code .= ']';
     $next = 'operator';
    break;
    case 'object':
     // this chunk is property of an object
     $code .= '->';
     if (is_array($chunk)) {
      // it's variable-in-variable
      $code .= '{'.$this->processVariableChunks($chunk).'}';
     } elseif (ctype_digit($chunk)) {
      // it's number
      $code .= '{'.$chunk.'}';
     } else {
      // it's string
      $code .= $chunk;
     }
     $next = 'operator';
    break;
    case 'operator':
     // this chunk is an operator
     switch ($chunk) {
      case '.':
       $next = 'array';
       // next chunk will be key of an array
      break;
      case '->':
       $next = 'object';
       // next chunk will be property of an object
      break;
      default:
       $this->raiseSyntaxError(TemplateError::E_UNEXPECTED, array(
        $chunk, '. or ->'
       ));
       // unexpected operator (shouldn't occur, though)
      break; // just for clarity
     }
    break;
   }
  }
  return $code;
 }
 /**
  * Transform filter chain into PHP code
  * @param $filters Array of filters to transform
  * @param $value Value to filter
  */
 public function processFilters(array $filters, $value) {
  $className = @$this->state->variables->className;
  $checkCode = '';
  foreach ($filters as &$filter) {
   if (empty($filter)) continue;
   list($filter, $arguments) = TemplateUtils::split(':', $filter);
   if (strpos($arguments, ',') !== false) {
    // arguments can be split by preg_split with no problems
    // (at least I haven't noticed any problems yet :))
    $arguments = preg_split('/(\".*?\")|\,/', $arguments, -1,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
   } elseif (empty($arguments)) {
    // if there isn't any argument, then use empty array
    $arguments = array();
   } else {
    // if there is single argument, convert it into array with itself
    $arguments = array($arguments);
   }
   // let's check if this filter is allowed
   if (!TemplateUtils::isAllowed($this->settings, $filter, 'securityFilters')) {
    $this->raiseSyntaxError(TemplateError::E_DISALLOWED, array($filter, 'filter'));
   }
   // now find filter in libraries
   // and construct proper code
   if (($libName = $this->loader->findLibrary($className, 'filters', $filter)) === false) {
    // if there's no library with handler for that filter
    // then raise syntax error
    $this->raiseSyntaxError(TemplateError::E_UNKNOWN_FILTER, array($filter));
   }
   // get handler name
   $callback = $this->loader->getCallback($libName, 'filters', $filter);
   $this->state->variables->metaVars->libraries[] = $libName;
   // use processFiltersArguments subroutine to create proper arguments code
   $arguments = $this->processFiltersArguments($arguments, $checkCode);
   $value = '$this->_lib_'.$libName.'->'.$callback.'('.$value.$arguments.')';
  }
  return array(
   'checkCode' => $checkCode,
   'variableCode' => $value
  );
 }
 /**
  * processFilters subroutine
  * @param $arguments Array
  * @param $check Reference to string buffer
  */
 protected function processFiltersArguments($arguments, &$check) {
  foreach ($arguments as &$argument) {
   if ($this->validator->isQuotedString($argument)) {
    // this is string, enclose it in apostrophes and escape
    $argument = '\''.TemplateUtils::escape(substr($argument, 1, -1)).'\'';
   } elseif ($this->validator->isNumber($argument, true)) {
    // this is integer or float, put as-is
   } else {
    // this is variable
    $argumentVar = $this->processVariable($argument);
    $check .= $argumentVar['variableCheck'];
    $argument = $argumentVar['variableCode'];
   }
  }
  return (count($arguments) > 0 ? ','.implode(',', $arguments) : '');
 }
 /**
  * Raise syntax error using current state
  * @param $code Integer
  * @param $params Array
  */
 public function raiseSyntaxError($code, array $params = array()) {
  throw new TemplateSyntaxError($this->getState(), $code, $params);
 }
 /**
  * Run handlers for given hook
  * @param $hook Hook name, string
  * @param $params Hook params, array
  */
 protected function runHooks($hook, array $params = array()) {
  foreach ($this->loader->getHooks(@$this->state->variables->className, $hook, $this) as $handler => $library) {
   call_user_func_array(array($library, $handler), $params);
  }
 }
}
/**
 * Default implementation of compiler state manager
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: Compiler.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateCompilerState implements ITemplateCompilerState {
 /**
  * Template source (0-based line numbering)
  */
 protected $source = array();
 /**
  * Currently open tags
  */
 protected $openTags = array();
 /**
  * Currently open blocks
  */
 protected $blockStack = array();
 /**
  * Already generated code
  */
 protected $code = array();
 /**
  * Initialization code
  */
 protected $initCode = '';
 /**
  * Current settings
  */
 protected $settings = array();
 /**
  * All template lines
  */
 protected $lines = 0;
 /**
  * Current template line
  */
 protected $line = 0;
 /**
  * State variables, ArrayObject
  */
 protected $variables = NULL;
 /**
  * Constructor
  * @param $settings Current settings
  */
 public function __construct(array $settings) {
  $this->settings = $settings;
  // setup state variables container with standard state variables
  $this->variables = new ArrayObject(array(
   'className' => '',
   'delimiterLengths' => new ArrayObject(array(
    'tagOpening' => strlen($settings['tagOpening']),
    'tagClosing' => strlen($settings['tagClosing']),
    'variableOpening' => strlen($settings['variableOpening']),
    'variableClosing' => strlen($settings['variableClosing']),
    'commentOpening' => strlen($settings['commentOpening']),
    'commentClosing' => strlen($settings['commentClosing']),
   ), ArrayObject::ARRAY_AS_PROPS),
   'metaVars' => new ArrayObject(array(
    'libraries' => array(),
    'usingI18n' => false
   ), ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST),
   'includes' => array(),
   'inComment' => false,
   'inLoop' => array()
  ), ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);
 }
 /**
  * Return current template source
  * @return Array
  */
 public function getSource() {
  return $this->source;
 }
 /**
  * Set new template source (and count lines)
  * @param $source Array
  */
 public function setSource(array $source) {
  $this->source = $source;
  $this->lines = count($source);
 }
 /**
  * Get one line from source
  * @param $line Integer
  * @return String
  */
 public function getLine($line = NULL) {
  if ($line == NULL) $line = $this->line;
  return ($line >= $this->lines ? NULL : $this->source[$line]);
 }
 /**
  * Get multiple lines from source
  * @param $start Integer
  * @param $end Integer
  * @return Array
  */
 public function getLines($start, $end) {
  return array_slice($this->source, (($start < 0) ? 0 : $start),
                                    (($end > $this->lines) ? $this->lines : $end), true);
 }
 /**
  * Get current line number
  * @return Integer
  */
 public function getCurrentLine() {
  return $this->line;
 }
 /**
  * Advance to next line
  */
 public function nextLine() {
  ++$this->line;
 }
 /**
  * Open new tag
  * @param $openingTag String
  * @param $closingTag String
  */
 public function openTag($openingTag, $closingTag) {
  $this->openTags[] = array('opening' => $openingTag,
                            'closing' => $closingTag,
                            'openLine' => $this->getCurrentLine());
 }
 /**
  * Verify and close tag
  * @param $openingTag String
  * @param $closingTag String
  */
 public function closeTag($openingTag, $closingTag) {
  $this->verifyTag($openingTag, $closingTag);
  array_pop($this->openTags);
 }
 /**
  * Verify tag
  * @param $openingTag String
  * @param $closingTag String
  */
 public function verifyTag($openingTag, $closingTag) {
  $tag = end($this->openTags);
  if ($tag['opening'] != $openingTag || $tag['closing'] != $closingTag) {
   throw new TemplateSyntaxError($this, TemplateError::E_MISPLACED_TAG,
                                 array(
                                  $closingTag,
                                  'Expected "'.$tag['closing'].'"',
                                 ));
  }
 }
 /**
  * Get all open tags
  * @param $asString Boolean
  * @return Array
  */
 public function getOpenTags($asString = false) {
  if (!$asString) {
   return $this->openTags;
  }
  $tags = array();
  foreach ($this->openTags as $tag) {
   $tags[] = '"'.$tag['opening'].'" opened on line '.$tag['openLine'];
  }
  return implode(', ', $tags);
 }
 /**
  * Enter logical block
  * @param $blockID String
  */
 public function enterBlock($blockID) {
  $this->blockStack[] = $blockID;
  $this->addCode('$b=\'\';');
 }
 /**
  * Exit logical block
  * @param $blockID String
  */
 public function exitBlock($blockID = NULL) {
  if ($blockID != NULL && ($this->getCurrentBlock() != $blockID)) {
   throw new TemplateSyntaxError($this, TemplateError::E_INVALID_DATA,
                                 array('unexpected exit from block '.$blockID.
                                       ', current block = '.$this->getCurrentBlock()));
  }
  $this->addCode('return $b;');
  array_pop($this->blockStack);
 }
 /**
  * Get all open blocks
  * @return Array
  */
 public function getOpenBlocks() {
  return $this->blockStack;
 }
 /**
  * Get current block ID
  * @return String
  */
 public function getCurrentBlock() {
  return end($this->blockStack);
 }
 /**
  * Add code chunk to current or given block
  * @param $code String
  * @param $blockID String
  */
 public function addCode($code, $blockID = NULL) {
  if ($this->variables->inComment) {
   return;
  }
  if ($blockID == NULL) $blockID = $this->getCurrentBlock();
  if (!isset($this->code[$blockID])) {
   $this->code[$blockID] = '';
  }
  $this->code[$blockID] .= $code;
 }
 /**
  * Add template's init code chunk (append if $prepend is false, prepend otherwise)
  * @param $code String
  * @param $prepend Boolean
  */
 public function addInitCode($code, $prepend = false) {
  if ($prepend) {
   $this->initCode = $code . $this->initCode;
  } else {
   $this->initCode .= $code;
  }
 }
 /**
  * Add plain text chunk to current or given block
  * @param $text String
  * @param $blockID String
  */
 public function addText($text, $blockID = NULL) {
  $text = '$b.=\''.TemplateUtils::escape($text).'\';';
  $this->addCode(str_replace('"\\n".\'\';', '"\\n";', $text), $blockID);
 }
 /**
  * Get code for given block
  * @param $blockID String
  * @return String
  */
 public function getCode($blockID) {
  if (!isset($this->code[$blockID])) return NULL;
  return $this->code[$blockID];
 }
 /**
  * Get template's init code
  * @return String
  */
 public function getInitCode() {
  return $this->initCode;
 }
 /**
  * Get all codes
  * @return Array
  */
 public function getAllCodes() {
  return $this->code;
 }
 /**
  * Get current settings
  * @return Array
  */
 public function getSettings() {
  return $this->settings;
 }
 /**
  * Pre 0.9 behaviour - get additional variable.
  * 0.9+ behaviour - proxy method, ensures that $this->variables is read-only.
  * @param $variable String (if $variable == 'variables', then 0.9+ behaviour is triggered)
  * @return Mixed, ArrayObject if 0.9+
  */
 public function __get($variable) {
  if ($variable == 'variables') return $this->variables;
  trigger_error('Using magical "__get" method for state variables access is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  if (!isset($this->variables[$variable])) return NULL;
  return $this->variables[$variable];
 }
 //
 // Deprecated pre-0.9 methods
 // 
 /**
  * Get delimiter length
  * @param $delimiter String
  * @return Integer
  * @deprecated Since 0.9
  */
 public function getLength($delimiter) {
  trigger_error('Method "getLength" is deprecated. Use "$state->variables->delimiterLengths" instead.',
                E_USER_WARNING);
  return $this->variables->delimiterLengths[$delimiter];
 }
 /**
  * Get parent template
  * @return String
  * @deprecated Since 0.9
  */
 public function getParent() {
  trigger_error('Method "getParent" is deprecated. Use "$state->variables->parentTemplate" instead.',
                E_USER_WARNING);
  return $this->variables->parentTemplate;
 }
 /**
  * Check for parent template
  * @return Boolean
  * @deprecated Since 0.9
  */
 public function hasParent() {
  trigger_error('Method "hasParent" is deprecated. Check for "$state->variables->parentTemplate" instead.',
                E_USER_WARNING);
  return (isset($this->variables->parentTemplate) && !empty($this->variables->parentTemplate));
 }
 /**
  * Set new parent template
  * @param $parent String
  * @return String
  * @deprecated Since 0.9
  */
 public function setParent($parent) {
  trigger_error('Method "setParent" is deprecated. Use "$state->variables->parentTemplate" instead.',
                E_USER_WARNING);
  $this->variables->parentTemplate = $parent;
 }
 /**
  * Return class name for this template
  * @return String
  * @deprecated Since 0.9
  */
 public function getClassName() {
  trigger_error('Method "getClassName" is deprecated. Use "$state->variables->className" instead.',
                E_USER_WARNING);
  return $this->variables->className;
 }
 /**
  * Set new class name for this template
  * @param $className String
  * @deprecated Since 0.9
  */
 public function setClassName($className) {
  trigger_error('Method "setClassName" is deprecated. Use "$state->variables->className" instead.',
                E_USER_WARNING);
  $this->variables->className = $className;
 }
 /**
  * Add template-wide include
  * @param $include String
  * @param $type String
  * @param $unique Boolean
  * @deprecated Since 0.9
  */
 public function addInclude($include, $type, $unique = true) {
  trigger_error('Method "addInclude" is deprecated. Use "$state->variables->metaVars" instead.',
                E_USER_WARNING);
  if (!isset($this->variables->includes[$type])) {
   $this->variables->includes[$type] = array();
  }
  if ($unique && array_search($include, $this->variables->includes[$type]) !== false) {
   return;
  }
  $this->variables->includes[$type][] = $include;
 }
 /**
  * Remove template-wide include
  * @param $include String
  * @param $type String
  * @deprecated Since 0.9
  */
 public function removeInclude($include, $type) {
  trigger_error('Method "removeInclude" is deprecated. Use "$state->variables->metaVars" instead.',
                E_USER_WARNING);
  if (!isset($this->variables->includes[$type]) || !in_array($include, $this->variables->includes[$type])) {
   return;
  }
  unset($this->variables->includes[$type][array_search($include, $this->variables->includes[$type])]);
 }
 /**
  * Get all includes
  * @return Array
  * @deprecated Since 0.9
  */
 public function getIncludes() {
  trigger_error('Method "getIncludes" is deprecated. Use "$state->variables->metaVars" instead.',
                E_USER_WARNING);
  return $this->variables->includes;
 }
 /**
  * Set additional variable
  * @param $additionalVar String
  * @param $value Mixed
  * @deprecated Since 0.9
  */
 public function __set($additionalVar, $value) {
  trigger_error('Method "__set" is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  $this->variables[$additionalVar] = $value;
 }
 /**
  * Delete additional variable
  * @param $additionalVar String
  * @deprecated Since 0.9
  */
 public function __unset($additionalVar) {
  trigger_error('Method "__unset" is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  unset($this->variables[$additionalVar]);
 }
 /**
  * Check for additional variable's existance
  * @param $additionalVar String
  * @deprecated Since 0.9
  */
 public function __isset($additionalVar) {
  trigger_error('Method "__isset" is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  return isset($this->variables[$additionalVar]);
 }
 /**
  * Push value to additional variable
  * @param $additionalVar String
  * @param $value Mixed
  * @deprecated Since 0.9
  */
 public function push($additionalVar, $value) {
  trigger_error('Method "push" is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  if (!is_array($this->variables[$additionalVar])) return;
  $this->variables[$additionalVar][] = $value;
 }
 /**
  * Pop value from additional variable
  * @param $additionalVar String
  * @return Mixed
  * @deprecated Since 0.9
  */
 public function pop($additionalVar) {
  trigger_error('Method "pop" is deprecated. Use "$state->variables" instead.',
                E_USER_WARNING);
  if (!is_array($this->variables[$additionalVar])) return;
  return array_pop($this->variables[$additionalVar]);
 }
}
/**
 * Helper class, containing validators for tag handlers (incl. compiler builtins)
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Compiler.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class TemplateValidator implements ITemplateValidator {
 /**
  * Checks if given argument is string enclosed in double quotes
  * @param $argument String
  * @return Boolean
  */
 public function isQuotedString($argument) {
  return (substr($argument, 0, 1) == '"' && substr($argument, -1, 1) == '"');
 }
 /**
  * Checks if given argument is integer or float
  * @param $argument String
  * @param $float Boolean
  * @return Boolean
  */
 public function isNumber($argument, $float = false) {
  return preg_match('/^\-?[0-9]+'.($float ? '(\.[0-9]+)?' : '').'$/', $argument);
 }
 /**
  * Checks if given argument is variable notation (with optional filter chain)
  * @param $argument String
  * @param $withFilters Boolean
  * @return Boolean
  */
 public function isVariable($argument, $withFilters = false, $prefix = '') {
  return preg_match('/^('.preg_quote($prefix, '/').'|[\w0-9\.\[\]]|\-\>)+'.
                    ($withFilters ? '(\|.+)*' : '').
                    '$/', $argument);
 }
}
