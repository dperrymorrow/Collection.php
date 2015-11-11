<?
class Collection {

  private $arr;
  private $type;

  const ASSOC = 'associative';
  const SEQUENCE = 'sequential';
  const OBJECT = 'object';

  function __construct($arr=[]){ 
    $arr = is_array($arr) ? $arr : (is_null($arr) ? [] : [$arr]);
    $this->arr = $this->isNumericArray($arr) ? $arr : [$arr];
    $this->type = $this->detectType();
  }

  public static function create($arr=[]) {
    return new Collection($arr);
  }

  function toArray() {
    return $this->arr;
  }
  
  function find($matchers) {
    return $this->where($matchers)->first();
  }

  function where($matchers) {
    $this->throwIfSequence();

    return $this->filter(function ($item) use ($matchers) {
      $fits = true;
      foreach ($matchers as $key => $value) {
        if ($this->getValue($item, $key) != $value) {
          $fits = false;
        }
      }
      return $fits;
    });
  }

  function reject($matchers) {
    return $this->filter(function ($item) use ($matchers) {
      $fits = true;
      foreach ($matchers as $key => $value) {
        if ($this->getValue($item, $key) == $value) {
          $fits = false;
        }
      }
      return $fits;
    });
  }

  function reverse() {
    $arr = $this->toArray();
    return Collection::create(array_reverse($arr));
  }

  // core methods
  function filter($method) {
    $filtered = Collection::create();
    foreach ($this->arr as $item) {
      if ($method($item) == true) {
        $filtered = $filtered->push($item); 
      }
    }
    return $filtered;
  }

  function rejectNull() {
    return $this->filter(function ($item) {
      return !is_null($item);
    });
  }

  function invoke($method, $args=null) {
    return $this->map(function ($item) use ($method, $args) {
      if (is_object($item)) {
        return is_null($args) ? $item->$method() : $item->$method($args);
      } else {
        return $item;
      }
    });
  }

  // almost all methods use this under the hood
  function map($method) {
    $mapped = $this->clear();
    foreach($this->arr as $item) {
      $mapped = $mapped->push($method($item));
    }
    return $mapped;   
  }

  function sortOn($key) {
    $arr = $this->toArray();
    usort($arr, $this->buildSorter($key));
    return Collection::create($arr);
  }

  function push($item) {
    $arr = $this->toArray();
    array_push($arr, $item);
    return Collection::create($arr);
  }

  function clear() {
    return Collection::create()->setType($this->type);
  }

  function copy() {
    return Collection::create($this->toArray());
  }

  function has($conditions) {
    return $this->where($conditions)->size() > 0;
  }

  function doesNothave($conditions) {
    return !$this->has($conditions);  
  }

  function unique($key=null) {
    if ($this->isEmpty()) {
      return $this;
    }

    if (($this->isObject() || $this->isAssoc()) && is_null($key)) {
      throw new \InvalidArgumentException("Collection->unique requires a key for arrays of objects.");
    }

    if ($key) {
      $unique = $this->clear();
      return $this->filter(function ($item) use ($key, &$unique) {
        if ($unique->doesNothave([$key => $this->getValue($item, $key)])) {
          $unique = $unique->push($item);
          return true;
        };
        return false;
      });

    } else {
      return Collection::create(array_unique($this->toArray()));
    }
  }
  
  function in($ids, $key="id") {
    return $this->filter(function ($item) use ($ids, $key) {
      return in_array($this->getValue($item, $key), $ids);
    });
  }

  function pluck($search, $unique=false) {
    if ($this->isEmpty()) return $this;
    $this->throwIfSequence();
    $unique = is_array($search) ? false : $unique;

    $plucked = $this->map(function ($item) use ($search) {
      if (is_array($search)) {
        $return = [];
        foreach($search as $field) {
          $return[$field] = $this->getValue($item, $field);
        }
        return $return;
      }
      return $this->getValue($item, $search);
    })->rejectNull();

    return $unique ? $plucked->unique() : $plucked;
  }

  function __call($method, $args=null) {
    if ($this->isEmpty()) return $this;

    if ($this->first() && method_exists($this->first(), $method)) {
      return $this->invoke($method, $args);
    } else {
      throw new \BadFunctionCallException("In Collection, the method $method was not found on the items in the Collections's array.");
    }
  }

  // does not return collection
  function size() { return count($this->arr); }
  function first() { return $this->isEmpty() ? null : $this->arr[0]; }
  function last() { return end($this->arr); }
  function isEmpty() { return empty($this->arr); }
  function isPresent() { return !$this->isEmpty(); }
  function getType() { return $this->type; }

  function setType($type) {
    $allowed = [self::OBJECT, self::SEQUENCE, self::ASSOC];
    if (!in_array($type, $allowed)) {
      throw new \InvalidArgumentException('you can only set the type of collection as ' . implode(", ", $allowed));
    }
    $this->type = $type;
    return $this;
  }
  
  // utilities
  private function getValue($item, $key) { 
    if (is_null($item)) return null;

    if ($this->isObject()) {
      return method_exists($item, $key) ? $item->$key() : $item->$key;
    } elseif ($this->isAssoc()) {
      return array_key_exists($key, $item) ? $item[$key] : null;
    }
    return $item;
  } 
  private function isObject() { return $this->type == self::OBJECT; }
  private function isSequence() { return $this->type == self::SEQUENCE; }
  private function isAssoc() { return $this->type == self::ASSOC; }

  private function isNumericArray($arr) { 
    foreach ($arr as $key => $value) {
      if (!is_int($key)) {
        return false;
      }
    }
    return true;
  }

  private function setArr($arr) {
    return Collection::create($arr);
  }

  private function findTestItem() {
    foreach($this->arr as $item) {
      if (!is_null($item)) {
        return $item;
      }
    }
    return null;
  }

  private function detectType() {
    $tester = $this->findTestItem();
    if (is_array($tester)) {
      return self::ASSOC;
    } else {
      return is_object($tester) ? self::OBJECT : self::SEQUENCE;
    }
  }

  private function throwIfSequence() {
    if ($this->isSequence() && !$this->isEmpty()) {
      throw new \BadFunctionCallException('You can only call where on collections consisting of objects or associative arrays.');
    }
  }

  private function buildSorter($key) {
    return function ($a, $b) use ($key) {
      return strnatcmp($this->getValue($a, $key), $this->getValue($b, $key));
    };
  }
}
