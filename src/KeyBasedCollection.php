<?
namespace Collection;
abstract class KeyBasedCollection extends Core {

  abstract protected function getValue($item, $key); 

  public function has($params) {
    return $this->where($params)->isPresent();
  }

  function find($matchers) {
    return $this->where($matchers)->first();
  }

  function where($matchers) {
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
        if ($this->getValue($item, $key) == $value) $fits = false;
      }
      return $fits;
    });
  }

  function sortOn($key) {
    $arr = $this->toArray();
    usort($arr, $this->buildSorter($key));
    return new $this->className($arr);
  }

  function in($ids, $key="id") {
    return $this->filter(function ($item) use ($ids, $key) {
      return in_array($this->getValue($item, $key), $ids);
    });
  }

  function pluck($search) {
    if ($this->isEmpty()) return $this;

    return $this->map(function ($item) use ($search) {
      if (is_array($search)) {
        $return = [];
        foreach($search as $field) {
          $return[$field] = $this->getValue($item, $field);
        }
        return $return;
      }
      return $this->getValue($item, $search);
    })->rejectNull();
  }

  function unique($key) {
    if ($this->isEmpty()) return $this;

    $unique = $this->clear();
    return $this->filter(function ($item) use ($key, &$unique) {
      if ($unique->doesNothave([$key => $this->getValue($item, $key)])) {
        $unique = $unique->push($item);
        return true;
      };
      return false;
    });
  }

  private function buildSorter($key) {
    return function ($a, $b) use ($key) {
      return strnatcmp($this->getValue($a, $key), $this->getValue($b, $key));
    };
  }

}