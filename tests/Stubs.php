<?
class TestObj {
  public $field = null;
  public $field2 = null;
  function __construct($field, $field2) { 
    $this->field = $field; 
    $this->field2 = $field2;
  }
  
  function retrieve($args) { 
    return $this->$args[0];
  }
}