<?
class CollectionTest extends PHPUnit_Framework_TestCase {
  // creation, constructor and static ::create
  public $objCol;
  public $assocCol;
  public $arrCol;

  function setup() {
    $this->objCol = Collection::create([
      new TestObj('donkey', 'pants'), 
      new TestObj('monkey', 'food'),
      new TestObj('cake', 'frosting'),
    ]);
    
    $this->assocCol = Collection::create([
      ['field' => 'donkey', 'field2' => 'pants'], 
      ['field' => 'monkey', 'field2' => 'food'],
      ['field' => 'cake', 'field2' => 'frosting'],
    ]);

    $this->arrCol = Collection::create([1,2,3]);
  }

  function testTypeDetection() {
    $this->assertEquals(Collection::SEQUENCE, $this->arrCol->getType(), 'flat array should be detected as such.');
    $this->assertEquals(Collection::OBJECT, $this->objCol->getType(), 'object array should be detected as such.');
    $this->assertEquals(Collection::ASSOC, $this->assocCol->getType(), 'assoc array should be detected as such.');
    $this->assertEquals(Collection::OBJECT, $this->objCol->push(null)->getType(), 'should find the type even with null in array.');
  }

  function testOperatingOnNull() {
    $col = $this->objCol->push(null)->push(null);
    $this->assertEquals($col->pluck('field', true)->toArray(), ['donkey', 'monkey', 'cake'], 'should not return null items');
  }

  function testManuallySettingType() {
    $col = new Collection([]);
    try {
      $col->setType('foobar');
    } catch (Exception $e) {
      $this->assertEquals('InvalidArgumentException', get_class($e), 'Should throw an error if you manually set the type one other than the 3 allowed');
    }
  }

  function testConstructingWithNullPassed() {
    $col = new Collection(null);
    $this->assertEquals($col->toArray(), [], 'if null passed empty array formed');
  }

  function testConstructingWithNonArrayPassed() {
    $col = new Collection(1);
    $this->assertEquals($col->toArray(), [1], 'if value that is not array passed, puts in array');
  }

  function testConstructingWithOneAssocArrayPassed() {
    $col = new Collection(['key' => 'val']);
    $this->assertEquals($col->toArray(), [['key' => 'val']], 'if value that is not array passed, puts in array');
  }

  function testStaticCreate() {
    $col = Collection::create(1);
    $this->assertEquals($col->toArray(), [1], 'if value that is not array passed, puts in array');
  }

  // accessors

  function testAccessMethods() {
    $arr = [1,2,3];
    $col = new Collection($arr);
    $this->assertEquals($col->toArray(), $arr, 'returns the contained array on toArray()');
    $this->assertEquals($col->size(), 3, 'returns the length of the contained array');
    $this->assertTrue($col->isPresent(), 'returns true if anything is in the array');
    $this->assertFalse($col->isEmpty(), 'returns false if anything is in the array');
    $this->assertEquals($col->first(), 1, 'returns first item in array');
    $this->assertEquals($col->last(), 3, 'returns last item in array');
  }

  // reject method

  function testRejectOnObjects() {
    $col = $this->objCol->reject(['field' => 'monkey']);
    $this->assertEquals($col->size(), 2, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'donkey', 'returns matching one search param');
    $this->assertEquals($this->objCol->size(), 3, 'does not effect original collection');
  }

  function testRejectOnMultipleFields() {
    $col = $this->objCol->reject(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 2, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'donkey', 'returns matching one search param');
    $this->assertEquals($col->last()->field2, 'frosting', 'returns matching one search param');
    $this->assertEquals($this->objCol->size(), 3, 'does not effect original collection');
  }

  // where method

  function testWhereMethodOnObjects() {
    $col = $this->objCol->where(['field' => 'monkey']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'monkey', 'returns matching one search param');
    $this->assertEquals($this->objCol->size(), 3, 'does not effect original collection');
  }

  function testWhereOnMultipleFields() {
    $col = $this->objCol->where(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()->field, 'monkey', 'returns matching one search param');
    $this->assertEquals($col->last()->field2, 'food', 'returns matching one search param');
    $this->assertEquals($this->objCol->size(), 3, 'does not effect original collection');
  }

  function testShouldThrowIfSequential() {
    try {
      $this->arrCol->where(['foo' => 'bar']);
    } catch (Exception $e) {
      $this->assertEquals('BadFunctionCallException', get_class($e), 'should throw an error if where called on sequential');
    }
  }

  function testWhereOnAssoc() {
    $col = $this->assocCol->where(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()['field'], 'monkey', 'returns matching one search param');
    $this->assertEquals($this->assocCol->size(), 3, 'does not effect original collection');
  }

  function testWhereOnAssocMismatc() {
    $col = $this->assocCol->where(['field' => 'monkey', 'field2' => 'nonexist']);
    $this->assertNull($col->first(), 'returns nothing if only one matches');
    $this->assertEquals($this->assocCol->size(), 3, 'does not effect original collection');
  }

  // reverse
  function testReverse() {
    $this->assertEquals($this->arrCol->reverse()->toArray(), [3,2,1], 'Should reverse the contained array');
    $this->assertEquals($this->arrCol->first(), 1, 'does not effect original collection');
  }

  function testFilter() {
    $result = $this->arrCol->filter(function ($item) {
      return $item == 1;
    });
    $this->assertEquals($result->toArray(), [1], 'returns only the items that return true from function');
    $this->assertEquals($this->arrCol->size(), 3, 'does not effect original collection');
  }

  // has and doesNotHave

  function testHas() {
    $this->assertTrue($this->objCol->has(['field' => 'monkey']), 'should find the object by field');
    $this->assertEquals($this->objCol->size(), 3, 'should not effect original collection');
  }

  function testDoesNotHas() {
    $this->assertTrue($this->objCol->doesNotHave(['field' => 'your mother']), 'should not find the object by field');
    $this->assertFalse($this->objCol->doesNotHave(['field' => 'donkey']), 'should find the object by field');
    $this->assertEquals($this->objCol->size(), 3, 'should not effect original collection');
  }

  // unique methods...

  function testUnique() {
    $col = Collection::create([1,2,2,1,1]);
    $this->assertEquals($col->unique()->toArray(), [1,2], 'Should pluck only unique from sequenced array items');
  }

  function testUniqueOnObjects() {
    $col = Collection::create([
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'pants'), 
      new TestObj('donkey', 'shirts')
    ]);
    
    $this->assertEquals($col->unique('field2')->size(), 2, 'Should pluck only unique from object array');
    $this->assertEquals($col->unique('field2')->pluck('field2')->toArray(), ['pants', 'shirts'], 'Should pluck only unique from object array');
    $this->assertEquals($col->size(), 4, 'should not change original collection');
  }

  function testUniqueOnAssoc() {
    $this->assertEquals($this->assocCol->unique('field2')->pluck('field2')->toArray(), ['pants', 'food', 'frosting'], 'Should pluck only unique items');
  }

  function testUniqueValidation() {
    try {
      $this->objCol->unique();
    } catch (Exception $error) { 
      $this->assertEquals(get_class($error), "InvalidArgumentException", 'if an assoc or obj, must pass a key');
    }
  }

  // in 
  function testInWithSequence() {
    $col = Collection::create([1,2,2,1,1]);
    $this->assertEquals($col->in([1])->toArray(), [1,1,1], 'should find items in a range on linear'); 
  }

  function testInWithAssoc() {
    $this->assertEquals($this->assocCol->in(['cake'], 'field')->toArray(), [['field' => 'cake', 'field2' => 'frosting']], 'should find items in a range on linear'); 
  }

  function testInWithObj() {
    $results = $this->objCol->in(['donkey'], 'field');
    $this->assertEquals($results->size(), 1, 'should have one match'); 
    $this->assertEquals($results->first()->field, 'donkey', 'should match'); 
  }

  // pluck
  function testPluckingOnSequence() {
    try {
      $col = Collection::create([1])->pluck(1);
    } catch (Exception $e) {
      $this->assertEquals('BadFunctionCallException', get_class($e), 'should not be able to call pluck on sequence');
    }
  }

  function testPluckingOnAssoc() {
    $result = $this->assocCol->pluck('field');
    $this->assertEquals($result->toArray(), ['donkey', 'monkey', 'cake'], 'should pluck the fields');
    $this->assertEquals($result->getType(), Collection::SEQUENCE, 'should now be a sequence');
    $this->assertEquals($this->assocCol->getType(), Collection::ASSOC, 'initial class should be unchanged');
  }

  function testPluckingOnObj() {
    $result = $this->objCol->pluck('field');
    $this->assertEquals($result->toArray(), ['donkey', 'monkey', 'cake'], 'should pluck the fields');
    $this->assertEquals($result->getType(), Collection::SEQUENCE, 'should now be a sequence');
    $this->assertEquals($this->objCol->getType(), Collection::OBJECT, 'initial class should be unchanged');
  }

  function testPluckingOnObjWithMultipleFields() {
    $result = $this->objCol->pluck(['field', 'field2']);
    $expectedResult = [
      ['field' => 'donkey', 'field2' => 'pants'], 
      ['field' => 'monkey', 'field2' => 'food'], 
      ['field' => 'cake', 'field2' => 'frosting']
    ];
    $this->assertEquals($result->toArray(), $expectedResult, 'should pluck the fields');
    $this->assertEquals($result->getType(), Collection::ASSOC, 'should now be a sequence');
    $this->assertEquals($this->objCol->getType(), Collection::OBJECT, 'initial class should be unchanged');
  }

  function testMagicMethods() {
    $this->assertEquals($this->objCol->retrieve('field')->toArray(), $this->objCol->pluck('field')->toArray(), 'should call methods on items');
  }

  function testMagicMethodsShouldSkipIfEmpty() {
    $this->assertEquals($this->objCol->clear()->retrieve('field')->toArray(), [], 'should not error if empty');
    $this->assertTrue($this->objCol->size() > 0, 'should not effect original object');
  }
}