<?php
/**
 * Core hook library.
 * @since 0.7.0-dev
 * @author PiotrLegnica
 * @license{New BSD License}
 * @version $Id: CoreHook.library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class CoreHookLibrary extends TemplateLibrary implements ITemplateCompileLibrary {
 /**
  * Return handlers for this library
  * @return Array
  */
 public static function getHandlers() {
  return array(
   'tags' => array(),
   'filters' => array(),
   'hooks' => array(
    'processVariable:preProcessVariableChunk' => array(
     'hookInternalVariable',
    ),
    'processVariable:postProcessVariableChunks' => array(
     'hookForLoopVariable',
     'hookInternalVariableCode',
    ),
    'processVariable:postCheckCode' => array(
     'hookInternalVariableCheck',
    ),
    'processToken:preTypeCheck' => array(
     'hookForComments',
    ),
    'processToken:postProcessFilterChain' => array(
     'hookAutoEscaping',
    )
   ),
  );
 }
 /** @hook{processVariable:postProcessVariableChunks} */
 public function hookForLoopVariable(&$code) {
  if (@$this->state->variables->inLoop && strpos($code, '[\'forloop\']') !== false) {
   $code = str_replace('[\'forloop\']', '[$f]', $code);
  }
 }
 /** @hook{processToken:preTypeCheck} */
 public function hookForComments(&$token) {
  if (@$this->state->variables->inComment && !($token['type'] == 'tag' && substr($token['contents'], 0, 10) == 'endcomment')) {
   $token['type'] = 'comment';
  }
 }
 // {{ internal }} handling
 /**
  * @hook{processVariable:preProcessVariableChunk}
  * @todo Some error checking would be nice
  */
 public function hookInternalVariable(&$variable) {
  $settings = $this->compiler->getManager()->getSettings();
  $prefixLen = strlen($settings['variablePrefix']);
  if (substr($variable, 0, 8 + $prefixLen) == $settings['variablePrefix'].'internal') {
   $this->state->variables->inInternalVariableHandling = true;
   $parts = explode('.', $variable); // only second/third part is important here
                                     // rest will be handled by hookInternalVariableCode
   switch ($parts[1]) {
    case 'request':
     $this->state->variables->inInternalVariable = 'request:'.$parts[2];
     $variable = $settings['variablePrefix'].substr($variable, 17 + $prefixLen);
    break;
    case 'const':
     $this->state->variables->inInternalVariable = 'const';
     $variable = $settings['variablePrefix'].substr($variable, 15 + $prefixLen);
    break;
    case 'version':
     $this->state->variables->inInternalVariable = 'version';
     $variable = $settings['variablePrefix'].'_'; // version will be hardcoded anyway
    break;
   }
  }
 }
 /** @hook{processVariable:postProcessVariableChunks} */
 public function hookInternalVariableCode(&$code) {
  if (@$this->state->variables->inInternalVariableHandling) {
   list($type, $data) = TemplateUtils::split(':', $this->state->variables->inInternalVariable);
   switch ($type) {
    case 'request':
     $code = str_replace('$this->ctx[\''.$data.'\']', '$_'.$data, $code);
    break;
    case 'const':
     $code = str_replace(array('$this->ctx[', ']'), array('constant(', ')'), $code);
    break;
    case 'version':
     $code = '\''.SITHTEMPLATE_VERSION.'\'';
    break;
   }
  }
 }
 /** @hook{processVariable:postCheckCode} */
 public function hookInternalVariableCheck(&$check) {
  if (@$this->state->variables->inInternalVariableHandling) {
   list($type, $data) = TemplateUtils::split(':', $this->state->variables->inInternalVariable);
   switch ($type) {
    case 'const':
    case 'version':
     $check = '';
    break;
   }
  }
  // this is last in chain, so cleanup
  if (isset($this->state->variables->inInternalVariableHandling)) {
   unset($this->state->variables->inInternalVariableHandling);
  }
  if (isset($this->state->variables->inInternalVariable)) {
   unset($this->state->variables->inInternalVariable);
  }
 }
 /** @hook{processToken:postProcessFilterChain} */
 public function hookAutoEscaping(array &$chain) {
  $settings = $this->compiler->getManager()->getSettings();
  if ($settings['enableSecurity'] && $settings['securityAutoEscape']) {
   if (!in_array('safe', $chain)) {
    // autoescaping enabled, variable not marked as safe
    // so append escape to filter chain
    $chain[] = 'escape';
   }
  }
  // always "safe" filter, as it's not implemented as real filter
  $chain = array_filter($chain, array($this, 'cleanSafe'));
 }
 /** hookAutoEscaping subroutine */
 protected function cleanSafe($element) {
  return ($element != 'safe');
 }
}
