<?
namespace Collection;
use PHPUnit_Framework_TestCase;
class AssociativeTest extends PHPUnit_Framework_TestCase {

  function setup() {
    $this->col = Associative::create([
      ['field' => 'donkey', 'field2' => 'pants'], 
      ['field' => 'monkey', 'field2' => 'food'],
      ['field' => 'cake', 'field2' => 'frosting'],
    ]);
  }

  function testTypeDetection() {
    $this->assertEquals('Associative', $this->col->getType(), 'object array should be detected as such.');
  }

  function testConstructingWithOneAssocArrayPassed() {
    $col = new Associative(['key' => 'val']);
    $this->assertEquals($col->toArray(), [['key' => 'val']], 'if value that is not array passed, puts in array');
  }

  function testWhereOnAssoc() {
    $col = $this->col->where(['field' => 'monkey', 'field2' => 'food']);
    $this->assertEquals($col->size(), 1, 'should have one item in the array');
    $this->assertEquals($col->first()['field'], 'monkey', 'returns matching one search param');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

  function testWhereOnAssocMismatc() {
    $col = $this->col->where(['field' => 'monkey', 'field2' => 'nonexist']);
    $this->assertNull($col->first(), 'returns nothing if only one matches');
    $this->assertEquals($this->col->size(), 3, 'does not effect original collection');
  }

  function testInWithAssoc() {
    $this->assertEquals($this->col->in(['cake'], 'field')->toArray(), [['field' => 'cake', 'field2' => 'frosting']], 'should find items in a range on linear'); 
  }

  function testUniqueOnAssoc() {
    $this->assertEquals($this->col->unique('field2')->pluck('field2')->toArray(), ['pants', 'food', 'frosting'], 'Should pluck only unique items');
  }

  function testPluckingOnAssoc() {
    $result = $this->col->pluck('field');
    $this->assertEquals($result->toArray(), ['donkey', 'monkey', 'cake'], 'should pluck the fields');
    $this->assertEquals($result->getType(), 'Numeric', 'should now be a Numeric Collection');
    $this->assertEquals($this->col->getType(), 'Associative', 'initial class should be unchanged');
  }

}