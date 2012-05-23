<?php
/**
 * Example library.
 * @since 0.6.0-dev
 * @author PiotrLegnica
 * @license{New BSD}
 * @version $Id: Example.library.php 715 2009-01-03 16:56:46Z piotrlegnica $
 */
class ExampleLibrary extends TemplateLibrary implements ITemplateCompileLibrary, ITemplateRuntimeLibrary {
 /**
  * Return handlers for this library
  * @return Array
  */
 public static function getHandlers() {
  return array(
   'filters' => array(
    'foo' => 'thisHandlerNameIsVeryLongAndDoesntEvenIncludeFilterName',
   ),
   'tags' => array(
    'foo' => 'exampleFooTagHandler',
   ),
   'hooks' => array(),
  );
 }
 /**
  * Handles example tag foo.
  * @tag{foo}
  */
 public function exampleFooTagHandler($name, array $args) {
  // as you can see, handler name doesn't have to be handleTag_<name> anymore
  $this->state->addText(__CLASS__.'::'.__FUNCTION__);
 }
 /**
  * Handles example filter foo.
  * @filter{foo}
  */
 public function thisHandlerNameIsVeryLongAndDoesntEvenIncludeFilterName($var) {
  return __CLASS__.'::'.__FUNCTION__.'('.$var.')';
 }
}
