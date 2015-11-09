# Collection
Lets you do things like pluck, unique, filter, map, reject on a collection of items in PHP.

## Example
Say you have an Associative Array of items like such.
````PHP
$myArray = [
	['name' => 'Mike',   'profession' => 'Doctor',     'hobby' => 'Golf'], 
	['name' => 'George', 'profession' => 'Programmer', 'hobby' => 'Guitar'],
	['name' => 'Fred',   'profession' => 'Accountant', 'hobby' => 'Travel'],
	['name' => 'David',  'profession' => 'Painter',    'hobby' => 'Hiking'],
];
````

You could perform the following once converted into a Collection

````PHP
$collect = Collection::create($myArray);
$collect->pluck('name')->toArray();
// ['Mike', 'George', 'Fred', 'David']

$collect->where(['profession' => 'Doctor'])->pluck('name')->first();
// Mike

$collect->sortOn('hobby')->pluck('hobby')->toArray();
// ['Golf', 'Guitar', 'Hiking', 'Travel']
````



#Usage Instructions




