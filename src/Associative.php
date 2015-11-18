<?
namespace Collection;
class Associative extends KeyBasedCollection {

  protected function getValue($item, $key) {
    if (is_null($item)) return null;
    return array_key_exists($key, $item) ? $item[$key] : null;
  }

}