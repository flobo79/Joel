<?php
/**
 * Base class for libraries.
 * @since 0.6.0-dev
 * @license{New BSD License}
 * @author PiotrLegnica
 * @version $Id: Library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
abstract class TemplateLibrary implements ITemplateLibrary {
 /**
  * ITemplateCompiler instance or NULL
  */
 protected $compiler = NULL;
 /**
  * ITemplateCompilerState instance or NULL
  */
 protected $state = NULL;
 /**
  * ITemplateValidator instance or NULL
  */
 protected $validator = NULL;
 /**
  * ITemplateManager instance or NULL
  */
 protected $manager = NULL;
 /**
  * Constructor.
  */
 public function __construct(array $options) {
  if (isset($options['compiler']) && TemplateUtils::doesImplement($options['compiler'], 'ITemplateCompiler')) {
   $this->compiler = $options['compiler'];
   $this->state = $this->compiler->getState();
   $this->validator = $this->compiler->getValidator();
  }
  if (isset($options['manager']) && TemplateUtils::doesImplement($options['manager'], 'ITemplateManager')) {
   $this->manager = $options['manager'];
  }
 }
 /**
  * Raise E_TOO_FEW_ARGUMENTS, helper
  * @param $name String
  * @param $arguments Array
  * @param $expectedCount Integer
  */
 protected final function checkArgumentCount($name, array $arguments, $expectedCount) {
  if ($this->compiler) {
   $count = count($arguments);
   if ($count < $expectedCount) {
    $this->compiler->raiseSyntaxError(TemplateError::E_TOO_FEW_ARGUMENTS,
                                      array($name, $expectedCount, $count));
   }
  }
 }
 /**
  * Raise E_INVALID_ARGUMENT, helper
  * @param $name String
  * @param $argument Mixed
  * @param $test Boolean
  * @param $explain String
  */
 protected final function checkArgument($name, $argument, $test, $explain) {
  if ($this->compiler && !$test) {
   $this->compiler->raiseSyntaxError(TemplateError::E_INVALID_ARGUMENT,
                                     array($argument, $name, $explain));
  }
 }
}