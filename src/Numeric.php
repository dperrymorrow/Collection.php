<?
namespace Collection;
class Numeric extends Core {

	public function has($params) {
    return in_array($params, $this->arr);
  }

  public function in($item) {
    return $this->has($item);
  }

  function unique() {
    return new $this->className(array_unique($this->arr));
  }
}