# kijtra/container
Simple php data container.  
No dependencies only one file.  

![Build Statuc](https://travis-ci.org/kijtra/container.svg?branch=master)

## Usage


### Create Instance

```php
use Kijtra\Container;
$container = new Container;
```

OR with data

```php
$container = new \Kijtra\Container(array(
    'parent' => array(
        'child' => 'value'
    )
));
```

### Set data

```php
use Kijtra\Container;

$container = new Container;

// Add nested data directly
$container->key1->key2->key3 = 'key3 value';

// and Add middle of the data
$container->key1->key4 = 'key4 value';

// the result is..

/*
object {
  ["key1"]=>
  object {
    ["key2"]=>
    object {
      ["key3"]=> "key3 value"
    }
    ["key4"]=> "key4 value"
  }
}
*/
```

### Get data

```php
use Kijtra\Container;

$container = new Container(array(
    'key1' => array(
        'key2' => 'Value!'
    )
));

echo $container->key1->key2; // "Value!"

echo $container->key1; // "" (empty)

echo $container['key1']['key2'];  // "Value!"
```


### Methods

#### name()

```php
// Get current data key name.
echo $container->key1->name();// "key1" (string)
```

#### parent()

```php
// use 'key2'
$key2 = $container->key1->key2;

// Can get 'key1' data.
$key1 = $key2->parent();

// and more
$container = $key2->parent()->parent();
```

#### arr()

```php
// Get data as Array.
$array = $container->arr();

// Can get nested too.
$array = $container->key1->arr();
```

#### has()

```php
var_dump($container->has('key1'));// true

// same of
var_dump(isset($container->key1));// true

// same of
var_dump(empty($container->key1));// false
```

#### count()

```php
$container->key1 = 'value1';
$container->key2 = 'value2';

echo $container->count();// 2

// same of
echo count($container);// 2
```


## :warning: Attention

```php
use Kijtra\Container;
$container = new Container(array(
    'key1' => array(
        'key2' => 'Value!'
    )
));

// !NOT WORK!
$data = array_values($container); // Warnng
// array_* function is not work.
// But can use this alternative code.
$data = array_values($container->arr());

// !NOT WORK!
$container->key3 = 'value';
$parent = $container->key3->parent(); // Fatal
// because key3 value is string.
```


## Installation

kijtra/container is only one file.  
[Download source file](https://github.com/kijtra/container/blob/master/src/Container.php) and include it.

```php
include('/path/to/Container.php');
```

OR use [Composer](https://getcomposer.org/).

```bash
composer require kijtra/container
```

## Requirement

PHP **>=5.4**  

Because use [SplObjectStorage::getHash](http://php.net/manual/ja/splobjectstorage.gethash.php) method.  
But no dependencies.
