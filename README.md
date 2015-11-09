# Collection
Lets you do things like pluck, unique, filter, map, reject on a collection of items in PHP.

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
// David, Fred, George, Jack, Mike

$collect->where(['profession' => 'Doctor'])->pluck('name')->first();
// Jack

$collect->sortOn('hobby')->pluck('hobby')->toArray();
// Golf, Golf, Guitar, Hiking, Travel

$collect->sortOn('hobby')->pluck('hobby')->unique()->toArray();
// Golf, Guitar, Hiking, Travel
````

keep reading for more detail and instructions of use of all the methods.



#Usage Instructions




