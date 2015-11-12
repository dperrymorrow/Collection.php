
<?
require __DIR__ . "./../Collection.php";
class DemoTest extends PHPUnit_Framework_TestCase {
  // creation, constructor and static ::create

  function setup() {
   $this->people = Collection::create([
      ['name' => 'Jack',   'job' => 'Doctor',     'hobby' => 'Golf'], 
      ['name' => 'Mike',   'job' => 'Doctor',     'hobby' => 'Golf'], 
      ['name' => 'George', 'job' => 'Programmer', 'hobby' => 'Guitar'],
      ['name' => 'Fred',   'job' => 'Accountant', 'hobby' => 'Travel'],
      ['name' => 'David',  'job' => 'Painter',    'hobby' => 'Hiking'],
    ]);

    $this->arrCol = Collection::create([1,2,3]);
  }

  function testPluck() {
    $this->trace('pluck', $this->people->pluck('job'), false);
    $this->trace('pluck multiple', $this->people->pluck(['name', 'job']));
  }

  function testMap() {
    $result = $this->people->filter(function ($item) {
      return strlen($item['name']) > 4;
    });
    $this->trace('map', $result);
  }

  function testWhere() {
    $result = $this->people->where(['job' => 'Doctor']);
    $this->trace('where', $result);
  }

  function testFind() {
    $result = $this->people->find(['job' => 'Doctor']);
    $this->trace('find', Collection::create($result));
  }

  function testToArray () {
    $result = $this->people->pluck('name');
    $this->trace('toArray', $result, false);
  }

  function trace($name, $collection, $assoc=true) {
    echo "$name\n[";
    if ($assoc) { 
      echo "\n";
      $final = [];
      $collection->map(function ($item) use ($collection, &$final) {
        $output = [];
        foreach($item as $key => $val) {
          array_push($output, "$key => $val");
        }
        array_push($final, "  [" . implode(', ', $output) . "]");
      });
      echo  implode(",\n", $final);
      echo "\n";
    } else {
      echo implode(", ", $collection->toArray());
    }
    echo "]\n";
  }

}