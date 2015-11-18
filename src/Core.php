<?
namespace Collection;
abstract class Core {

  public $arr;
  public $className;

  function __construct($arr=[]){ 
    $this->arr = self::validateArray($arr);
    $this->className = get_class($this);
  }

  public static function create($arr=[]) {
    return new static($arr);
  }

  public static function autoBuild($arr) {
    $class = "\Collection\\" . self::findClassForArray($arr);
    return new $class($arr);
  }

  abstract public function has($params);

  public function doesNothave($params) {
    return !$this->has($params);
  }

  function toArray() {
    return $this->arr;
  }
  
  function reverse() {
    $arr = $this->toArray();
    return new static(array_reverse($arr));
  }

  function filter($method) {
    $filtered = [];
    foreach ($this->arr as $item) {
      if ($method($item) == true) {
        array_push($filtered, $item); 
      }
    }
    return new static($filtered);
  }

  function rejectNull() {
    return $this->filter(function ($item) {
      return !is_null($item);
    });
  }

  // almost all methods use this under the hood
  function map($method) {
    $mapped = [];
    foreach($this->arr as $item) {
      array_push($mapped, $method($item));
    }
    return self::autoBuild($mapped);   
  }

  function push($item) {
    $arr = $this->toArray();
    array_push($arr, $item);
    return new $this->className($arr);
  }

  function clear() { 
    return new $this->className([]);
  }

  function copy() { 
    return new $this->className($this->toArray()); 
  }
  
  // does not return collection
  function size() { return count($this->arr); }
  function first() { return $this->isEmpty() ? null : $this->arr[0]; }
  function last() { return end($this->arr); }
  function isEmpty() { return empty($this->arr); }
  function isPresent() { return !$this->isEmpty(); }
  function getType() { return str_replace("Collection\\", '', get_class($this)); }


  protected static function validateArray($arr) {
    if (is_null($arr)) $arr = [];
    if (!is_array($arr) || !self::isNumericArray($arr)) $arr = [$arr];
    return $arr;
  }

  protected static function findClassForArray($arr) {
    $arr = self::validateArray($arr);
    $types = [];

    foreach($arr as $item) {
      if (!is_null($item)) {
        $type = is_object($item) ? 'Object' : (is_array($item) ? 'Associative' : 'Numeric');
        array_push($types, $type);
      }
    }

    $types = array_unique($types);
    if (count($types) > 1)
      throw new \Exception('You cannot have mixed items in a Collection. you have ' . implode(' &', $types) . ' in your Collection');

    return $types[0];
  }

  protected static function isNumericArray($arr) { 
    foreach (array_keys($arr) as $key) {
      if (!is_int($key)) return false;
    }
    return true;
  }
}
