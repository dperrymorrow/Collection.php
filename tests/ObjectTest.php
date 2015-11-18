<?
namespace Collection;
use PHPUnit_Framework_TestCase;

// fake class for testing
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

class ObjectTest extends PHPUnit_Framework_TestCase {

  function setup() {
    $this->col = Object::create([
      new TestObj('donkey', 'pants'), 
      new TestObj('monkey', 'food'),
      new TestObj('cake', 'frosting'),
    ]);
  }

  function testTypeDetection() {
    $this->assertEquals('Object', $this->col->getType(), 'object array should be detected as such.');
  }

  function testOperatingOnNull() {
    $col = $this->col->push(null)->push(null);
    $this->assertEquals($col->pluck('field', true)->toArray(), ['donkey', 'monkey', 'cake'], 'should not return null items');
  }

  function testWhereOnMultipleFields() {
    $col = $this->col->where(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'monkey', 'returns matching one search param');
    $this->assertEquals($col->last()->field2, 'food', 'returns matching one search param');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

  function testHas() {
    $this->assertTrue($this->col->has(['field' => 'monkey']), 'should find the object by field');
    $this->assertEquals($this->col->size(), 3, 'should not effect original collection');
  }

  function testDoesNotHave() {
    $this->assertTrue($this->col->doesNotHave(['field' => 'your mother']), 'should not find the object by field');
    $this->assertFalse($this->col->doesNotHave(['field' => 'donkey']), 'should find the object by field');
    $this->assertEquals($this->col->size(), 3, 'should not effect original collection');
  }

  function testUniqueO() {
    $col = Object::create([
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'shirts')
    ]);

    $this->assertEquals($col->unique('field2')->size(), 2, 'Should pluck only unique from object array');
    $this->assertEquals($col->unique('field2')->pluck('field2')->toArray(), ['pants', 'shirts'], 'Should pluck only unique from object array');
    $this->assertEquals($col->size(), 4, 'should not change original collection');
  }

  function testInWithObj() {
    $results = $this->col->in(['donkey'], 'field');
    $this->assertEquals($results->size(), 1, 'should have one match'); 
    $this->assertEquals($results->first()->field, 'donkey', 'should match'); 
  }

  function testPluck() {
    $result = $this->col->pluck('field');
    $this->assertEquals($result->toArray(), ['donkey', 'monkey', 'cake'], 'should pluck the fields');
    $this->assertEquals($result->getType(), 'Numeric', 'should now be a sequence');
    $this->assertEquals($this->col->getType(), 'Object', 'initial class should be unchanged');
  }

  function testPluckingWithMultipleFields() {
    $result = $this->col->pluck(['field', 'field2']);
    $expectedResult = [
      ['field' => 'donkey', 'field2' => 'pants'], 
      ['field' => 'monkey', 'field2' => 'food'], 
      ['field' => 'cake', 'field2' => 'frosting']
    ];
    $this->assertEquals($result->toArray(), $expectedResult, 'should pluck the fields');
    $this->assertEquals($result->getType(), 'Associative', 'should now be a sequence');
    $this->assertEquals($this->col->getType(), 'Object', 'initial class should be unchanged');
  }

  function testMagicMethods() {
    $this->assertEquals($this->col->retrieve('field')->toArray(), $this->col->pluck('field')->toArray(), 'should call methods on items');
  }

  function testMagicMethodsShouldSkipIfEmpty() {
    $this->assertEquals($this->col->clear()->retrieve('field')->toArray(), [], 'should not error if empty');
    $this->assertTrue($this->col->size() > 0, 'should not effect original object');
  }

  function testReject() {
    $col = $this->col->reject(['field' => 'monkey']);
    $this->assertEquals($col->size(), 2, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'donkey', 'returns matching one search param');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

  function testRejectOnMultipleFields() {
    $col = $this->col->reject(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 2, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'donkey', 'returns matching one search param');
    $this->assertEquals($col->last()->field2, 'frosting', 'returns matching one search param');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }


  function testWhereMethodOnObjects() {
    $col = $this->col->where(['field' => 'monkey']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'monkey', 'returns matching one search param');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

}