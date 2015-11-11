# Collection
**Problem:** PHP can be annoying iterating over arrays over and over and cluttering up your code.

**Solution:** Collection lets you do things like pluck, unique, filter, map, reject on a collection of items in PHP.

**A Collection will work with arrays of the following types.**

- an Array of associative Arrays
- an Array of Objects such as database result set
- a single array of items such as strings or integers

## Example
Say you have an Associative Array of items like such. 

````PHP
$myArray = [
	['name' => 'Jack',   'profession' => 'Doctor',     'hobby' => 'Golf'], 
	['name' => 'Mike',   'profession' => 'Doctor',     'hobby' => 'Golf'], 
	['name' => 'George', 'profession' => 'Programmer', 'hobby' => 'Guitar'],
	['name' => 'Fred',   'profession' => 'Accountant', 'hobby' => 'Travel'],
	['name' => 'David',  'profession' => 'Painter',    'hobby' => 'Hiking'],
];
````

You could perform the following once converted into a Collection

````PHP
$collect = Collection::create($myArray);
$collect->sortOn('name')->pluck('name')->toArray();
// ['David', 'Fred', 'George', 'Jack', 'Mike']

$collect->where(['profession' => 'Doctor'])->pluck('name')->first();
// Jack

$collect->sortOn('hobby')->pluck('hobby')->toArray();
// ['Golf', 'Golf', 'Guitar', 'Hiking', 'Travel']

$collect->sortOn('hobby')->pluck('hobby')->unique()->toArray();
// ['Golf', 'Guitar', 'Hiking', 'Travel']
````

keep reading for more detail and instructions of use of all the methods.



#Methods
| Methods | Parameters | Description  |
| ------  | -----------| ------------ |
| [pluck](#pluck) | ```$keys```, A single string, or Array of keys | Pulls keys from assoc Arrays or Objects |
| [filter](#filter) | an anonymous function. | Filters the collection based on the Boolean return value of the function passed. |


##Pluck
pulls certain keys _(if Associative)_ or fields _(if a Collection of Objects)_
will throw an error if it is just a Numeric Array. Will remove any Null values collected using [rejectNull()](#rejectNull) method.

````php
$people = [
	['name' => 'Jack',   'job' => 'Doctor',     'hobby' => 'Golf'], 
	['name' => 'Mike',   'job' => 'Doctor',     'hobby' => 'Golf'], 
	['name' => 'George', 'job' => 'Programmer', 'hobby' => 'Guitar'],
	['name' => 'Fred',   'job' => 'Accountant', 'hobby' => 'Travel'],
	['name' => 'David',  'job' => 'Painter',    'hobby' => 'Hiking'],
];

Collection::create($people)->pluck('profession')->toArray();
// ['Doctor', 'Doctor', 'Programmer', 'Accountant', 'Painter']

// with optional second parameter to limit to unique items.
Collection::create($people)->pluck('profession', true)->toArray();
// ['Doctor', 'Programmer', 'Accountant', 'Painter']

// with multiple keys.
Collection::create($people)->pluck(['name', 'profession'])->toArray();
/*
[
  ['name' => 'Jack',   'job' => 'Doctor'],
  ['name' => 'Mike',   'job' => 'Doctor'],
  ['name' => 'George', 'job' => 'Programmer'],
  ['name' => 'Fred',   'job' => 'Accountant'],
  ['name' => 'David',  'job' => 'Painter']     
]
*/
````



