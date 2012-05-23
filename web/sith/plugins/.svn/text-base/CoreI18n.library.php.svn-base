<?php
/**
 * Core i18n library.
 * @since 0.9.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: CoreI18n.library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class CoreI18nLibrary extends TemplateLibrary implements ITemplateCompileLibrary,
                                                         ITemplateRuntimeLibrary {
 /**
  * Return handlers for this library
  * @return Array
  */
 public static function getHandlers() {
  return array(
   'tags' => array(
    'tr' => 'handleTr',
    'ntr' => 'handleNTr',
   ),
   'filters' => array(
    'tr' => 'handleTr',
    'ntr' => 'handleNTr',
   ),
   'hooks' => array(),
  );
 }
 /** @tag{tr} */
 public function handleTr($name, array $args) {
  // {% tr "translatable string" [arg1 [arg2 [...]]] %}
  $this->checkArgumentCount($name, $args, 1);
  $this->checkArgument($name, $args[0],
                       $this->validator->isQuotedString($args[0]),
                       'expected quoted string');
  $trStr = TemplateUtils::escape(substr(array_shift($args), 1, -1));
  $this->state->variables->metaVars->usingI18n = true;
  // sanitize args
  // everything should be a variable
  foreach ($args as &$arg) {
   $this->checkArgument($name, $arg,
                        $this->validator->isVariable($arg),
                        'expected a variable');
   list($variable, $filters) = TemplateUtils::split('|', $arg);
   $variable = $this->compiler->processVariable($variable);
   $filteredVariable = $this->compiler->processFilters(
                        $this->compiler->processFilterChain($filters),
                        $variable['variableCode']
                       );
   $this->state->addCode($variable['variableCheck']);
   $this->state->addCode($filteredVariable['checkCode']);
   $arg = $filteredVariable['variableCode'];
  }
  // create translation code
  $this->state->addCode('$b.=$this->i18n->tr(\''.$trStr.'\'');
  if (!empty($args)) {
   $this->state->addCode(','.implode(',', $args));
  }
  $this->state->addCode(');');
 }
 /** @tag{ntr} */
 public function handleNTr($name, array $args) {
  // {% ntr "translatable string (singular)" "translatable string (plural)" countArg [arg1 [arg2 [...]]] %}
  $this->checkArgumentCount($name, $args, 3);
  $this->checkArgument($name, $args[0],
                       $this->validator->isQuotedString($args[0]),
                       'expected quoted string');
  $this->checkArgument($name, $args[1],
                       $this->validator->isQuotedString($args[1]),
                       'expected quoted string');
  $this->checkArgument($name, $args[2],
                       $this->validator->isVariable($args[2]),
                       'expected a variable');
  $trSingular = TemplateUtils::escape(substr(array_shift($args), 1, -1));
  $trPlural = TemplateUtils::escape(substr(array_shift($args), 1, -1));
  $this->state->variables->metaVars->usingI18n = true;
  // parse countArg argument
  $countVar = array_shift($args);
  list($variable, $filters) = TemplateUtils::split('|', $countVar);
  $variable = $this->compiler->processVariable($variable);
  $filteredVariable = $this->compiler->processFilters(
                       $this->compiler->processFilterChain($filters),
                       $variable['variableCode']
                      );
  $this->state->addCode($variable['variableCheck']);
  $this->state->addCode($filteredVariable['checkCode']);
  $countVar = $filteredVariable['variableCode'];
  unset($variable, $filters, $filteredVariable); // cleanup
  // sanitize args
  // everything should be a variable
  foreach ($args as &$arg) {
   $this->checkArgument($name, $arg,
                        $this->validator->isVariable($arg),
                        'expected a variable');
   list($variable, $filters) = TemplateUtils::split('|', $arg);
   $variable = $this->compiler->processVariable($variable);
   $filteredVariable = $this->compiler->processFilters(
                        $this->compiler->processFilterChain($filters),
                        $variable['variableCode']
                       );
   $this->state->addCode($variable['variableCheck']);
   $this->state->addCode($filteredVariable['checkCode']);
   $arg = $filteredVariable['variableCode'];
  }
  // create translation code
  $this->state->addCode('$b.=$this->i18n->ntr(\''.$trSingular.'\',\''.$trPlural.'\','.$countVar);
  if (!empty($args)) {
   $this->state->addCode(','.implode(',', $args));
  }
  $this->state->addCode(');');
 }
}
