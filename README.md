# Collection.php
**Problem:** PHP can be annoying iterating over arrays over and over and cluttering up your code.

**Solution:** Collection lets you do things like pluck, unique, filter, map, reject on a collection of items in PHP. 

Collection allows for chaining, which greatly reduces your code. Also the original collection is uneffected as you will see in the examples below, as a new Collection is returned on each method invoked.

**A Collection will work with arrays of the following types.**

- an Array of associative Arrays
- an Array of Objects such as database result set
- a single array of items such as strings or integers

## Example
Say you have an Associative Array of items like such. 

```php
$people = [
    ['name' => 'Jack',   'job' => 'Doctor',     'hobby' => 'Golf'], 
    ['name' => 'Mike',   'job' => 'Doctor',     'hobby' => 'Golf'], 
    ['name' => 'George', 'job' => 'Programmer', 'hobby' => 'Guitar'],
    ['name' => 'Fred',   'job' => 'Accountant', 'hobby' => 'Travel'],
    ['name' => 'David',  'job' => 'Painter',    'hobby' => 'Hiking'],
];
```

You could perform the following once converted into a Collection

```php
$collect = Collection::create($people);
$collect->sortOn('name')->pluck('name')->toArray();
// ['David', 'Fred', 'George', 'Jack', 'Mike']

$collect->where(['profession' => 'Doctor'])->pluck('name')->first();
// Jack

$collect->sortOn('hobby')->pluck('hobby')->toArray();
// ['Golf', 'Golf', 'Guitar', 'Hiking', 'Travel']

$collect->sortOn('hobby')->pluck('hobby')->unique()->toArray();
// ['Golf', 'Guitar', 'Hiking', 'Travel']
```

keep reading for more detail and instructions of use of all the methods.

#Methods
| Methods | Parameters | Returns  |
| ------  | -----------| ------------ |
| [create](#create) | Array | a Collection object |
| [toArray](#toArray) | none | An Array |
| [pluck](#pluck) | String, or Array of keys | Collection of just those key values. |
| [filter](#filter) | an anonymous function. | Collection where the return value of the function was true. |
| [where](#where) | associative array | Collection of items that match the key values passed. |


##Create 
> ```Collection::create(Array)``` build a new Collection Object. 

Alias for ```new Collection(Array)``` using the static build Functiion allows for chaining off the contstructor

Build the Collection from your Array of Associative Arrays, Objects such as Database result set, or Numeric Array _(sequential)_

```php
$people = Collection::create([
  ['name' => 'Jack',   'job' => 'Doctor',     'hobby' => 'Golf'], 
  ['name' => 'Mike',   'job' => 'Doctor',     'hobby' => 'Golf'], 
  ['name' => 'George', 'job' => 'Programmer', 'hobby' => 'Guitar'],
  ['name' => 'Fred',   'job' => 'Accountant', 'hobby' => 'Travel'],
  ['name' => 'David',  'job' => 'Painter',    'hobby' => 'Hiking'],
]);
```
    
##toArray 
> ```->toArray()``` returns the Array that the Collection Object contains.
    
```php
$people->pluck('name')->toArray();
[Jack, Mike, George, Fred, David]
```

##Pluck 
> ``` ->pluck(String or Array)``` pulls certain keys _(if Associative)_ or fields _(if a Collection of Objects)_

will throw an error if it is just a Numeric Array. Will remove any Null values collected using [rejectNull()](#rejectNull) method.

As with any keys passed to Collection functions, the key can represent a key in an associative Array, a attribute on an Object, or a Method on an Object.

````php  
$people->pluck('profession')->toArray();
//  [Doctor, Doctor, Programmer, Accountant, Painter]

// with multiple keys.
$people->pluck(['name', 'profession'])->toArray();
/*  
[
    [name => Jack, job => Doctor],
    [name => Mike, job => Doctor],
    [name => George, job => Programmer],
    [name => Fred, job => Accountant],
    [name => David, job => Painter]
]
*/
```

##Filter 
> ```php ->filter(Function)``` Filters the Collection based on the Boolean return value of the Anonymous function passed. 
    
If your method returns true, the item will remain, if false, it will be removed from the returned Collection.

As with any Closure in PHP, you can access variables outside of the function using the use operator. 
See [the PHP documentation on the subject.](http://php.net/manual/en/functions.anonymous.php)

```php  
// filter the collection to items wher name length is greater than 4.
$people->filter(function($item) {
  return strlen($item['name']) > 4;
})->toArray();
/*
[
  [name => George, job => Programmer, hobby => Guitar],
  [name => David, job => Painter, hobby => Hiking]
]
*/
```

##Where ```->where(Array)```
> Returns a Collection consisting of items that meet the key values passed. 

Works the same for Collections of Object attributes or Associative Array keys.
```php    
// find all the doctors.
$people->where(['job' => 'Doctor'])->toArray();
/*
[
  [name => Jack, job => Doctor, hobby => Golf],
  [name => Mike, job => Doctor, hobby => Golf]
]
*/
```

##Find 
> ```->find(Array)``` Returns an Associative Array, the first item found that matches the key vals passed. 
> Alias for ```->where(Array)->first()```

```php
// find the first doctor.
$people->find(['job' => 'Doctor'])->toArray(); 
// [name => Jack, job => Doctor, hobby => Golf]
```




