<?
namespace Collection;
use PHPUnit_Framework_TestCase;
class NumericTest extends PHPUnit_Framework_TestCase {
  
  function setup() {
    $this->col = Numeric::create([1,2,3]);
  }

  function testTypeDetection() {
    $this->assertEquals('Numeric', $this->col->getType(), 'flat array should be detected as such.');
  }

  function testConstructingWithNullPassed() {
    $col = new Numeric(null);
    $this->assertEquals($col->toArray(), [], 'if null passed empty array formed');
  }

  function testConstructingWithNonArrayPassed() {
    $col = new Numeric(1);
    $this->assertEquals($col->toArray(), [1], 'if value that is not array passed, puts in array');
  }

  function testStaticCreate() {
    $col = Numeric::create(1);
    $this->assertEquals($col->toArray(), [1], 'if value that is not array passed, puts in array');
  }

  function testAccessMethods() {
    $arr = [1,2,3];
    $col = new Numeric($arr);
    $this->assertEquals($col->toArray(), $arr, 'returns the contained array on toArray()');
    $this->assertEquals($col->size(), 3, 'returns the length of the contained array');
    $this->assertTrue($col->isPresent(), 'returns true if anything is in the array');
    $this->assertFalse($col->isEmpty(), 'returns false if anything is in the array');
    $this->assertEquals($col->first(), 1, 'returns first item in array');
    $this->assertEquals($col->last(), 3, 'returns last item in array');
  }
  
  function testReverse() {
    $this->assertEquals($this->col->reverse()->toArray(), [3,2,1], 'Should reverse the contained array');
    $this->assertEquals($this->col->first(), 1, 'does not effect original collection');
  }

  function testFilter() {
    $result = $this->col->filter(function ($item) {
      return $item == 1;
    });
    $this->assertEquals($result->toArray(), [1], 'returns only the items that return true from function');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

  function testUnique() {
    $col = Numeric::create([1,2,2,1,1]);
    $this->assertEquals($col->unique()->toArray(), [1,2], 'Should pluck only unique from sequenced array items');
  }  

  function testInWithSequence() {
    $col = Numeric::create([1,2,2,1,1]);
    $this->assertTrue($col->has(1), 'should find items in a range on linear'); 
  }
  
}