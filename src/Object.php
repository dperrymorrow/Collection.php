<?
namespace Collection;
class Object extends KeyBasedCollection {

  protected function getValue($item, $key) {
    if (is_null($item)) return null;

    if (property_exists($item, $key)) {
      return $item->$key;
    } elseif (method_exists($item, $key)) {
      return $item->$key();
    }

    return null;
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

  function __call($method, $args=null) {
    if ($this->isEmpty()) return $this;

    if (method_exists($this->first(), $method))
      return $this->invoke($method, $args);
  }
	
}