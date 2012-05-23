<?php
/**
 * Core tag library.
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: CoreTag.library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class CoreTagLibrary extends TemplateLibrary implements ITemplateCompileLibrary {
 /**
  * Return handlers for this library
  * @return Array
  */
 public static function getHandlers() {
  return array(
   'tags' => array(
    'block'       => 'handleBlock',
    'endblock'    => 'handleEndBlock',
    'comment'     => 'handleComment',
    'endcomment'  => 'handleEndComment',
    'cycle'       => 'handleCycle',
    'debug'       => 'handleDebug',
    'extends'     => 'handleExtends',
    'filter'      => 'handleFilter',
    'endfilter'   => 'handleEndFilter',
    'firstof'     => 'handleFirstOf',
    'for'         => 'handleFor',
    'endfor'      => 'handleEndFor',
    'if'          => 'handleIf',
    'endif'       => 'handleEndIf',
    'else'        => 'handleElse',
    'elseif'      => 'handleElseIf',
    'include'     => 'handleInclude',
    'now'         => 'handleNow',
    'templatetag' => 'handleTemplateTag',
    'widthratio'  => 'handleWidthRatio',
    'with'        => 'handleWith',
    'endwith'     => 'handleEndWith',
    'putblock'    => 'handlePutBlock',
    'call'        => 'handleCall',
   ),
   'filters' => array(),
   'hooks' => array(),
  );
 }
 /**
  * Used to create unique names for for loops (array).
  */
 private $loopNameCounters = array();
 //
 // Django standard tags
 //
 /** @tag{block} */
 public function handleBlock($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       ctype_alnum($args[0]),
                       'expected non-quoted string');
  $blockID = TemplateUtils::sanitize($args[0]);
  if (!in_array('store', $args)) {
   // if storing, then don't include code to run block
   $this->state->addCode('$b.=$this->_block_'.$blockID.'();');
  }
  // open tag and enter block
  $this->state->openTag('block', 'endblock');
  $this->state->enterBlock('block:'.$blockID);
  if (in_array('append', $args)) {
   // append to previous block
   if (isset($this->state->variables->parentTemplate) && !empty($this->state->variables->parentTemplate)) {
    $this->state->addCode('$b.=parent::_block_'.$blockID.'();');
   } else {
    $this->compiler->raiseSyntaxError(TemplateError::E_INVALID_ARGUMENT,
                                      array('append', $name,
                                            'unexpected "append" (no parent template)'));
   }
  }
 }
 /** @tag{endblock} */
 public function handleEndBlock($name, array $args) {
  $this->state->closeTag('block', 'endblock');
  $this->state->exitBlock();
 }
 /** @tag{comment} */
 public function handleComment($name, array $args) {
  $this->state->openTag('comment', 'endcomment');
  // comments are handled by compiler
  $this->state->variables->inComment = true;
 }
 /** @tag{endcomment} */
 public function handleEndComment($name, array $args) {
  $this->state->closeTag('comment', 'endcomment');
  $this->state->variables->inComment = false;
 }
 /** @tag{cycle} */
 public function handleCycle($name, array $args) {
  // new implementation, based on Django dev
  // {% cycle "row1" "row2" rowvar %} - in loop
  // {% cycle "row1" "row2" rowvar as rowcycle %} - outside loop, initialization
  // {% cycle rowcycle %} - outside loop, reuse
  $inLoop = (isset($this->state->variables->inLoop) && !empty($this->state->variables->inLoop));
  $this->checkArgumentCount($name, $args, ($inLoop ? 2 : 1));
  if (@$this->state->variables->inLoop) {
   $cycleName = end($this->state->variables->inLoop).TemplateUtils::sanitize(implode(' ',$args));
   if (!$this->state->getCode('cycle:'.$cycleName)) {
    // not initialized yet, so create code
    $code = $this->handleCycleSub($args);
    $this->state->addCode($code, 'cycle:'.$cycleName);
   }
  } else {
   $argCount = count($args);
   if ($argCount > 1) {
    // arguments are present, initialize
    $this->checkArgumentCount($name, $args, 4); // var1 var2 as cycle
    $this->checkArgument($name, $args[$argCount-2],
                         ($args[$argCount-2] == 'as'),
                         'expected "as"');
    $cycleName = TemplateUtils::sanitize($args[$argCount-1]);
    if ($this->state->getCode('cycle:'.$cycleName) !== NULL) {
     $this->compiler->raiseSyntaxError(TemplateError::E_INVALID_TAG,
                                       array($name, 'redefined cycle "'.$cycleName.'"'));
    }
    $code = $this->handleCycleSub(array_slice($args, 0, -2));
    $this->state->addCode($code, 'cycle:'.$cycleName);
   } else {
    // arguments aren't present, reuse
    $cycleName = TemplateUtils::sanitize($args[0]);
    if (!$this->state->getCode('cycle:'.$cycleName)) {
     $this->compiler->raiseSyntaxError(TemplateError::E_INVALID_TAG,
                                       array($name, 'unknown cycle "'.$cycleName.'"'));
    }
   }
  }
  $this->state->addCode('$b.=$this->_cycle_'.$cycleName.'();');
 }
 /**
  * handleCycle subroutine
  */
 protected function handleCycleSub(array $args) {
  $check = '';
  $values = array();
  $code = 'static $a=NULL;static $c=NULL;static $i=0;';
  $code .= 'if($a==NULL){$a=array(';
  foreach ($args as $arg) {
   if ($this->validator->isQuotedString($arg)) {
    $values[] = '\''.TemplateUtils::escape(substr($arg, 1, -1)).'\'';
   } else {
    $var = $this->compiler->processVariable($arg);
    $values[] = $var['variableCode'];
    $check .= $var['variableCheck'];
   }
  }
  $code .= implode(',',$values);
  $code .= ');$c=count($a);}if($i>=$c){$i=0;}return $a[$i++];';
  return $check.$code;
 }
 /** @tag{debug} */
 public function handleDebug($name, array $args) {
  $this->state->addCode('var_dump($this->ctx);');
 }
 /** @tag{extends} */
 public function handleExtends($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       $this->validator->isQuotedString($args[0]),
                       'expected quoted string');
  $this->state->variables->parentTemplate = substr($args[0], 1, -1);
 }
 /** @tag{filter} */
 public function handleFilter($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $filters = $this->compiler->processFilterChain(implode('', $args));
  $this->state->openTag('filter', 'endfilter');
  $md5 = md5($args[0]);
  $this->state->addCode('$b.=$this->_filter_'.TemplateUtils::sanitize($md5).'();');
  $this->state->enterBlock('filter:'.$md5);
  $this->state->variables->activeFilters = $filters;
 }
 /** @tag{endfilter} */
 public function handleEndFilter($name, array $args) {
  $this->state->closeTag('filter', 'endfilter');
  $filteredBlock = $this->compiler->processFilters(
                    $this->state->variables->activeFilters,
                    '$b'
                   );
  $this->state->addCode('$b='.$filteredBlock['variableCode'].';');
  $this->state->exitBlock();
  unset($this->state->variables->activeFilters);
 }
 /** @tag{firstof} */
 public function handleFirstOf($name, array $args) {
  $this->checkArgumentCount($name, $args, 2);
  $code = '';
  $check = '';
  $settings = $this->compiler->getManager()->getSettings();
  foreach ($args as $arg) {
   $this->checkArgument($name, $arg,
                        $this->isVariable(str_replace($settings['variablePrefix'], '', $arg)),
                        'expected non-quoted string');
   $var = $this->compiler->processVariable($arg);
   $code .= 'elseif('.$var['variableCode'].'){$b.='.$var['variableCode'].';}';
   $check .= $var['variableCheck'];
  }
  $this->state->addCode($check); // add checking code
  $this->state->addCode(substr($code, 4)); // remove "else" from first if
 }
 /**
  * @tag{for}
  * @bug Ugly workarounds for #4
  */
 public function handleFor($name, array $args) {
  $this->checkArgumentCount($name, $args, 3);
  $settings = $this->compiler->getManager()->getSettings();
  $this->checkArgument($name, $args[0],
                       preg_match('/(\w+)(\,?)(\w+)?/i', str_replace($settings['variablePrefix'], '', $args[0]), $matches),
                       'expected non-quoted string');
  $inArg = 1; // used to check for "in"
  if (!empty($matches[2]) && !isset($matches[3])) {
   // if we have comma but don't have second part, then
   // ensure next argument is variable, too
   $this->checkArgument($name, $args[1],
                        (!($args[1] == 'in' && $args[2] != 'in') &&
                         $this->isVariable($args[1])),
                        'expected second variable');
   $keyVariable = str_replace($settings['variablePrefix'], '', $matches[1]);
   $valueVariable = str_replace($settings['variablePrefix'], '', $args[1]);
   ++$inArg;
  } elseif (!empty($matches[2]) && isset($matches[3]) && !empty($matches[3])) {
   // it's key,value
   $keyVariable = str_replace($settings['variablePrefix'], '', $matches[1]);
   $valueVariable = str_replace($settings['variablePrefix'], '', $matches[3]);
  } else {
   // only value
   $keyVariable = false;
   $valueVariable = str_replace($settings['variablePrefix'], '', $args[0]);
  }
  // check for "in"
  $this->checkArgument($name, $args[$inArg],
                       ($args[$inArg] == 'in'),
                       'expected "in"');
  // check for variable
  $this->checkArgument($name, $args[$inArg+1],
                       $this->isVariable($args[$inArg+1], true),
                       'expected variable (with optional filter chain)');
  // parse iterated variable
  list($strVariable, $filters) = TemplateUtils::split('|', $args[$inArg+1]);
  $variable = $this->compiler->processVariable($strVariable);
  $filteredVariable = $this->compiler->processFilters(
                       $this->compiler->processFilterChain($filters),
                       $variable['variableCode']
                      );
  
  // creating a unique name for the loops method
  $loopNameBase = TemplateUtils::sanitize($keyVariable.'_'.$valueVariable.'_'.$strVariable);  
  if(!isset($this->loopNameCounters[$loopNameBase]))
   $this->loopNameCounters[$loopNameBase] = 0;
  $loopName = $loopNameBase . $this->loopNameCounters[$loopNameBase]++;
  
  // enter loop block
  // if we're inside other loop, then we need parent loop's 'forloop' context variable
  $code = '$b.=$this->_loop_'.$loopName.'(';
  if ($this->state->variables->inLoop) {
   $code .= '$f';
  }
  $code .= ');';
  $this->state->addCode($code);
  // now, enter block
  $this->state->openTag('for', 'endfor');
  $this->state->enterBlock('loop:'.$loopName);
  // and create loop code
  // initialization code
  // setup variable names
  //
  // $k = value of key
  // $v = value of value :)
  // $kn = name of key context variable
  // $vn = name of value context variable
  // $ic = item count in iterated variable
  // $iv = iterated variable
  // $f = name of forloop context variable
  // $pf = contents of parent forloop variable
  // $this->ctx[$kn] = reference to $k
  // $this->ctx[$vn] = reference to $v
  //
  $this->state->addCode('$f=\'forloop_'.TemplateUtils::escape($loopName).'\';');
  // variable checks
  $this->state->addCode($variable['variableCheck']);
  $this->state->addCode($filteredVariable['checkCode']);
  // filtered variable cache
  $this->state->addCode('$iv='.$filteredVariable['variableCode'].';');
  // check if we have iterator or array
  // if not, handle it properly
  $this->state->addCode('if(!is_array($iv)&&!(is_object($iv)&&TemplateUtils::doesImplement('.
                        '$iv,\'Traversable\')&&TemplateUtils::doesImplement($iv,'.
                        '\'Countable\'))){return $this->variableIsInvalid('.
                        '\''.$strVariable.'\',\''.$name.' '.implode(' ', $args).'\','.
                        '\'tag\',\'expected array or iterator\');}');
  if ($keyVariable) {
   $this->state->addCode('$k=\'\';');
   $this->state->addCode('$kn=\''.TemplateUtils::escape($keyVariable).'\';');
  }
  $this->state->addCode('$v=\'\';');
  $this->state->addCode('$vn=\''.TemplateUtils::escape($valueVariable).'\';');
  if ($keyVariable) {
   $this->state->addCode('$this->ctx[$kn]=&$k;');
  }
  $this->state->addCode('$this->ctx[$vn]=&$v;');
  // initialize forloop variable
  $this->state->addCode('$ic=count($iv);');
  $this->state->addCode('if(func_num_args()==1){$pf=func_get_arg(0);}else{$pf=NULL;}');
  $this->state->addCode('$this->ctx[$f]=array(\'counter\'=>1,\'counter0\'=>0,');
  $this->state->addCode('\'revcounter\'=>$ic,\'revcounter0\'=>$ic-1,\'first\'=>true,');
  // forloop.parent is deprecated
  $this->state->addCode('\'last\'=>$ic-1==0);if($pf){$this->ctx[$f][\'parent\']=&$this->ctx[$pf];');
  $this->state->addCode('$this->ctx[$f][\'parentloop\']=&$this->ctx[$pf];}');
  // create loop
  $this->state->addCode('foreach($iv as '.($keyVariable ? '$k=>': '').'$v){');
  if (!isset($this->state->variables->inLoop)) {
   $this->state->variables->inLoop = array();
  }
  
  $this->state->variables->inLoop[] = $loopName;
 }
 /** @tag{endfor} */
 public function handleEndFor($name, array $args) {
  $this->state->closeTag('for', 'endfor');
  array_pop($this->state->variables->inLoop);
  // advance counters
  $this->state->addCode('++$this->ctx[$f][\'counter\'];');
  $this->state->addCode('++$this->ctx[$f][\'counter0\'];');
  $this->state->addCode('--$this->ctx[$f][\'revcounter\'];');
  $this->state->addCode('--$this->ctx[$f][\'revcounter0\'];');
  // first/last check
  $this->state->addCode('$this->ctx[$f][\'first\']=false;');
  $this->state->addCode('$this->ctx[$f][\'last\']='.
                       '($this->ctx[$f][\'revcounter0\']==0);');
  // end and deinitialize loop
  $this->state->addCode('}');
  $this->state->addCode('unset($this->ctx[$f]);');
  $this->state->addCode('if(isset($kn)){unset($this->ctx[$kn]);}');
  $this->state->addCode('unset($this->ctx[$vn]);');
  // exit loop block
  $this->state->exitBlock();
 }
 /** @tag{if} */
 public function handleIf($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->state->openTag('if', 'endif');
  // open code block
  $code = $this->parseIfExpression($name, $args);
  // add codes to current state
  $this->state->addCode('if('.$code.'){');
 }
 /**
  * parseIfExpression subroutine - discard empty tokens
  */
 protected function parseIfExpressionNonEmpty($arg) {
  return ($arg != '' && $arg != ' ');
 }
 /**
  * handleIf subroutine - parse expression
  */
 protected function parseIfExpression($name, array $args) {
  // prepare arguments
  // old regex
  // $args = preg_split('/([\(\)])|(\"\w+\")|((?:[\[\]\w\d.]|\-\>)+((?:\.|\-\>)[\[\]\w\d]+(\|[^\|]+)*)*)/',
  //                    implode(' ', $args), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
  // new regex
  $args = preg_split('/(\".+?\")|([()])|([^\s()]+)/', implode(' ', $args), -1,
                     PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
  $args = array_filter($args, array($this, 'parseIfExpressionNonEmpty'));
  // prepare
  $code = '';
  $level = 0;
  // syntax checking
  $_operators = array('eq','neq','lt','lte','gt','gte','and','or');
  $_parentheses = array('(',')');
  $nextAllowed = array_merge($_parentheses, array('variable', 'not'));
  // process arguments
  foreach ($args as $arg) {
   if (in_array($arg, $_parentheses)) {
    // encountered parenthesis
    if (!in_array($arg, $nextAllowed)) {
     $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                       array($arg, implode(' or ', $nextAllowed)));
    }
    switch ($arg) {
     case '(': // open sub-expression
      $level++;
      $code .= '(';
      $nextAllowed = array('variable', 'not', '(');
     break;
     case ')': // close sub-expression
      if (--$level < 0) {
       // too much closing parentheses
       $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                         array($arg, implode(' or ', $nextAllowed)));
      }
      $code .= ')';
      $nextAllowed = array_merge($_operators, array(')'));
     break;
    }
   } elseif (in_array($arg, $_operators)) {
    // encountered operator (except "not")
    if (!in_array($arg, $nextAllowed)) {
     $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                       array($arg, implode(' or ', $nextAllowed)));
    }
    switch ($arg) {
     case 'eq':  $code .= '=='; break; // equals
     case 'neq': $code .= '!='; break; // not equals
     case 'lt':  $code .= '<'; break;  // less than
     case 'lte': $code .= '<='; break; // less than or equals
     case 'gt':  $code .= '>'; break;  // greater than
     case 'gte': $code .= '>='; break; // greater than or equals
     case 'and': $code .= '&&'; break; // logical and
     case 'or':  $code .= '||'; break; // logical or
    }
    $nextAllowed = array('variable', 'not', '(');
   } elseif ($arg == 'not') {
    // encountered "not"
    if (!in_array($arg, $nextAllowed)) {
     $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                       array($arg, implode(' or ', $nextAllowed)));
    }
    $code .= '!';
    $nextAllowed = array_merge($_operators, array('variable', '('));
   } else {
    // encountered variable or literal value
    if (!in_array('variable', $nextAllowed)) {
     $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                       array($arg, implode(' or ', $nextAllowed)));
    }
    if ($this->validator->isQuotedString($arg)) {
     // literal string
     $code .= '\''.substr($arg, 1, -1).'\'';
    } elseif ($this->validator->isNumber($arg, true)) {
     // integer/float
     $code .= $arg;
    } elseif ($this->isVariable($arg, true)) {
     // variable
     list($variable, $filters) = TemplateUtils::split('|', $arg);
     $variable = $this->compiler->processVariable($variable);
     $filteredVariable = $this->compiler->processFilters(
                          $this->compiler->processFilterChain($filters),
                          $variable['variableCode']
                         );
     $code .= $filteredVariable['variableCode'];
    } else {
     $this->compiler->raiseSyntaxError(TemplateError::E_UNEXPECTED,
                                       array($arg, implode(' or ', $nextAllowed)));
    }
    $nextAllowed = array_merge($_operators, array(')'));
   }
  }
  if ($level > 0) {
   $this->compiler->raiseSyntaxError(TemplateError::E_INVALID_TAG,
                                     array($name,
                                     'unclosed '.$level.' parentheses found '.
                                     'in an expression'));
  }
  return $code;
 }
 /** @tag{endif} */
 public function handleEndIf($name, array $args) {
  $this->state->closeTag('if', 'endif');
  $this->state->addCode('}');
 }
 /**
  * @tag{elseif}
  */
 public function handleElseIf($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->state->verifyTag('if', 'endif');
  // open code block
  $code = $this->parseIfExpression($name, $args);
  // add codes to current state
  $this->state->addCode('}elseif('.$code.'){');
 }
 /**
  * @tag{else}
  */
 public function handleElse($name, array $args) {
  $this->state->verifyTag('if', 'endif');
  $this->state->addCode('}else{');
 }
 /** @tag{include} */
 public function handleInclude($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       ($this->validator->isQuotedString($args[0]) ||
                       $this->isVariable($args[0])),
                       'expected quoted string or variable');
  // construct code
  $code = '$b.=$this->manager->includeTemplate(';
  $check = '';
  if ($this->validator->isQuotedString($args[0])) {
   $code .='\''.TemplateUtils::escape(substr($args[0], 1, -1)).'\'';
  } else {
   $var = $this->compiler->processVariable($args[0]);
   $code .= $var['rawVariableCode'];
   $check .= $var['variableCheck'];
  }
  $code .= ')->runTemplate($this->ctx);';
  // add code
  $this->state->addCode($check);
  $this->state->addCode($code);
 }
 /** @tag{now} */
 public function handleNow($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       $this->validator->isQuotedString($args[0]),
                       'expected quoted string');
  $this->state->addCode('$b.=date(\''.TemplateUtils::escape(substr($args[0], 1, -1)).'\');');
 }
 /** @tag{templatetag} */
 public function handleTemplateTag($name, array $args) {
  static $map = array(
   // Django
   'openblock' => 'tagOpening',
   'closeblock' => 'tagClosing',
   'openvariable' => 'variableOpening',
   'closevariable' => 'variableClosing',
   'opencomment' => 'commentOpening',
   'closecomment' => 'commentClosing',
   // SithTemplate 0.6.x
   'ob' => 'tagOpening',
   'cb' => 'tagClosing',
   // SithTemplate 0.5.x
   'opentag' => 'tagOpening',
   'ot' => 'tagOpening',
   'closetag' => 'tagClosing',
   'ct' => 'tagClosing',
   'openvar' => 'variableOpening',
   'ov' => 'variableOpening',
   'closevar' => 'variableClosing',
   'cv' => 'variableClosing',
   'oc' => 'commentOpening',
   'cc' => 'commentClosing',
  );
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       ctype_alnum($args[0]),
                       'expected non-quoted string');
  if (isset($map[$args[0]])) {
   $this->state->addCode('$_s=$this->manager->getSettings();');
   $this->state->addCode('$b.=$_s[\''.TemplateUtils::escape($map[$args[0]]).'\'];');
   $this->state->addCode('unset($_s);');
  }
 }
 /** @tag{widthratio} */
 public function handleWidthRatio($name, array $args) {
  $this->checkArgumentCount($name, $args, 3); // value maxValue constant
  // ratio = round((value/maxValue)*constant)
  $this->checkArgument($name, $args[0],
                       ($this->isVariable($args[0], true) ||
                        $this->validator->isNumber($args[0], true)),
                       'expected variable or float');
  $this->checkArgument($name, $args[1],
                       ($this->isVariable($args[1], true) ||
                        $this->validator->isNumber($args[1], true)),
                       'expected variable or float');
  $this->checkArgument($name, $args[2],
                       $this->validator->isNumber($args[2]),
                       'expected integer');
  // construct code
  $code = '$b.=strval(round((';
  $check = '';
  foreach (array_slice($args, 0, 2) as $arg) {
   if ($this->validator->isNumber($arg, true)) {
    $code .= $arg;
   } elseif ($this->isVariable($arg, true)) {
    list($variable, $filters) = TemplateUtils::split('|', $arg);
    $variable = $this->compiler->processVariable($variable);
    $filteredVariable = $this->compiler->processFilters(
                         $this->compiler->processFilterChain($filters),
                         $variable['variableCode']
                        );
    $code .= $filteredVariable['variableCode'];
    $check .= $variable['variableCheck'].$filteredVariable['checkCode'];
   }
   $code .= '/';
  }
  $code = substr($code, 0, -1).')*'.$args[2].'));';
  // add code
  $this->state->addCode($check);
  $this->state->addCode($code);
 }
 /** @tag{with} */
 public function handleWith($name, array $args) {
  $this->checkArgumentCount($name, $args, 3);
  $this->checkArgument($name, $args[0],
                       $this->isVariable($args[0]),
                       'expected variable');
  $this->checkArgument($name, $args[1],
                       ($args[1] == 'as'),
                       'expected "as"');
  $this->checkArgument($name, $args[2],
                       $this->isVariable($args[2]),
                       'expected variable');
  $blockName = TemplateUtils::sanitize(implode(' ', $args));
  // enter block
  $this->state->openTag('with', 'endwith');
  $this->state->addCode('$b.=$this->_with_'.$blockName.'();');
  $this->state->enterBlock('with:'.$blockName);
  // parse variables
  list($variable, $filters) = TemplateUtils::split('|', $args[0]);
  // first variable
  $variable = $this->compiler->processVariable($variable);
  $filteredVariable = $this->compiler->processFilters(
                       $this->compiler->processFilterChain($filters),
                       $variable['variableCode']
                      );
  // second (new) variable
  $secondVariable = $this->compiler->processVariable($args[2]);
  $this->state->addCode($variable['variableCheck']);
  $this->state->addCode($filteredVariable['checkCode']);
  $this->state->addCode($secondVariable['rawVariableCode'].'='.
                        $filteredVariable['variableCode'].';');
  if (!isset($this->state->variables->inWith)) {
   $this->state->variables->inWith = array();
  }
  $this->state->variables->inWith[] = $secondVariable['rawVariableCode'];
 }
 /** @tag{endwith} */
 public function handleEndWith($name, array $args) {
  $this->state->closeTag('with', 'endwith');
  $variable = array_pop($this->state->variables->inWith);
  $this->state->addCode('unset('.$variable.');');
  $this->state->exitBlock();
 }
 //
 // SithTemplate standard tags
 //
 /** @tag{putblock} */
 public function handlePutBlock($name, array $args) {
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       ctype_alnum($args[0]),
                       'expected non-quoted string');
  $blockID = TemplateUtils::sanitize($args[0]);
  // construct code
  // block existance checking code
  $this->state->addCode('if(is_callable(array($this,\'_block_'.TemplateUtils::escape($blockID).'\'))){');
  // block inserting code
  $this->state->addCode('$b.=$this->_block_'.$blockID.'();}');
 }
 /** @tag{call} */
 public function handleCall($name, array $args) {
  // {% call foo.bar->baz as callResult %} - call $this->ctx['foo']['bar']->baz()
  //                                         and put result into $this->ctx['callResult']
  // {% call "md5" foo.bar as callResult %} - call md5($this->ctx['foo']['bar'])
  //                                          and put result into $this->ctx['callResult']
  // {% call "str_replace" "foo" "bar" foo %} - call str_replace('foo', 'bar',
  //                                            $this->ctx['foo']) and display
  //                                            result
  $this->checkArgumentCount($name, $args, 1);
  $code = '';
  $check = '';
  if (in_array('as', $args)) {
   // saving result to context variable
   $asArg = count($args)-2;
   $this->checkArgument($name, $args[$asArg],
                        ($args[$asArg] == 'as'),
                        'expected "as"');
   $this->checkArgument($name, $args[$asArg+1],
                        $this->isVariable($args[$asArg+1]),
                        'expected variable');
   $var = $this->compiler->processVariable($args[$asArg+1]);
   $code .= $var['rawVariableCode'].'=';
   $args = array_slice($args, 0, -2);
  } else {
   // displaying result
   $code .= '$b.=';
  }
  // determine function to call
  $func = array_shift($args);
  if ($this->validator->isQuotedString($func)) {
   // literal string
   $func = substr($func, 1, -1);
   $settings = $this->compiler->getManager()->getSettings();
   if (!TemplateUtils::isAllowed($settings, $func, 'securityFunctions')) {
    $this->compiler->raiseSyntaxError(TemplateError::E_DISALLOWED, array($func, 'function'));
   }
   $check .= 'if(!is_callable(\''.TemplateUtils::escape($func).'\')){';
   $check .= 'throw new TemplateRuntimeError(TemplateError::E_INVALID_DATA,';
   /** @todo DRY */
   $check .= 'array(\''.TemplateUtils::escape($func).' used as function name but ';
   $check .= 'isn\\\'t callable \'));}';
   $code .= $func.'(';
  } else {
   // context variable
   list($variable, $filters) = TemplateUtils::split('|', $func);
   $rawVariable = $variable;
   $variable = $this->compiler->processVariable($variable);
   $rpos = strrpos($variable['rawVariableCode'], '->');
   $dpos = strrpos($variable['rawVariableCode'], ']');
   if (($rpos !== false) && ($rpos > $dpos)) {
    // -> is last
    // we need to create array for is_callable
    list($ar, $ob) = TemplateUtils::split('->', $variable['rawVariableCode'], true);
    $check .= '$_fn=array('.$ar.', \''.TemplateUtils::escape($ob).'\');';
    $check .= 'if(isset('.$variable['rawVariableCode'].')){';
    $check .= '$_fn=&'.$variable['rawVariableCode'].';}';
   } else {
    $check .= '$_fn=&'.$variable['rawVariableCode'].';';
    $check .= 'if(!TemplateUtils::isAllowed($this->settings,$_fn,\'securityFunctions\')){';
    $check .= 'throw new TemplateRuntimeError(TemplateError::E_DISALLOWED,array(';
    $check .= '$_fn,\'function\'));}';
   }
   $check .= 'if(!is_callable($_fn)){';
   $check .= 'throw new TemplateRuntimeError(TemplateError::E_INVALID_DATA,';
   $check .= 'array(\''.TemplateUtils::escape($rawVariable).' used as function name';
   $check .= ' but isn\\\'t callable\'));}';
   $code .= 'call_user_func($_fn'.(count($args)>0?',':'');
  }
  // now parse arguments
  foreach ($args as &$arg) {
   $arg = $this->handleCallSub($arg, $check);
  }
  $code .= implode(',', $args);
  $code .= ');unset($_fn);';
  // add code
  $this->state->addCode($check);
  $this->state->addCode($code);
 }
 /** handleCall subroutine */
 protected function handleCallSub($argument, &$check) {
  if ($this->validator->isQuotedString($argument)) {
   // this is string, enclose it in apostrophes and escape
   $argument = '\''.TemplateUtils::escape(substr($argument, 1, -1)).'\'';
  } elseif ($this->validator->isNumber($argument, true)) {
   // this is integer or float, put as-is
  } else {
   // this is variable
   list($variable, $filters) = TemplateUtils::split('|', $argument);
   $variable = $this->compiler->processVariable($variable);
   $filteredVariable = $this->compiler->processFilters(
                        $this->compiler->processFilterChain($filters),
                        $variable['variableCode']
                       );
   $check .= $variable['variableCheck'];
   $check .= $filteredVariable['checkCode'];
   $argument = $filteredVariable['variableCode'];
  }
  return $argument;
 }
 /**
  * Common helper
  * @bug Workaround for issue #4
  * @todo Remove ASAP
  */
 private function isVariable($var, $filters = false) {
  $settings = $this->compiler->getManager()->getSettings();
  return $this->validator->isVariable($var, $filters, $settings['variablePrefix']);
 }
}
