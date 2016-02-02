# BellevueRouter [![Build Status](https://travis-ci.org/Archer70/BellevueRouter.svg?branch=master)](https://travis-ci.org/Archer70/BellevueRouter)
A small router with path variable support.

## Examples

#### Route to regular function

**Routes**
```php
$routes = [
    '/home' => [
        'function' => 'main'
    ]
];
```

**PHP**
```php
<?php
use BellevueRouter\src\Router;

require_once 'routes.php';

$router = new Router($routes);
$router->route('/home');

function main()
{
    echo 'Hello!';
}
```
#### Route to a static class method

**Routes**
```php
$routes = [
    '/home' => [
        'class' => 'StaticRoutes',
        'method' => 'main'
    ]
];
```
**PHP**
```php
<?php
use BellevueRouter\src\Router;

require_once 'routes.php';

$router = new Router($routes);
$router->route('/home');

class StaticRoutes
{
    public static function main()
    {
        echo 'Static class method.'
    }
}
```

#### Route to an object instance method.

**Routes**
```php
$routes = [
    '/home' => [
        'object' => 'Routes',
        'method' => 'sayHi'
    ]
];
```
**PHP**
```php
<?php
use BellevueRouter\src\Router;

require_once 'routes.php';

$router = new Router($routes);
$router->route('/home');

class Routes
{
    public static function sayHi()
    {
        echo 'Object instance method.'
    }
}
```

#### Path Variables to Function Arguments

Arguments to be passed to the function are given in the arguments array. They will be passed to the function in the same order as defined here, so if your function definition looks like `myFunc($var1, $var2)` and your arguments array is `['val1, 'val2']`, myFunc will recieve those values in the $var1 and $var2 variables.

You can also inject path values into your function via named sections of your path. That is, if you have a path like `/post/{id}`, the router will look in your arguments array for a string with the same name: `{id}`, and replace it with the value the user puts in the path. So if the user hits path `/post/12`, and you have your arguments array like `['{id}']`, the **string** "12" would be passed as the first function argument.

The example below show full argument usage.

**Routes**
```php
$clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$routes = [
    '/hi/{name}' => [
        'function' => 'hi',
        'arguments' => ['{name}', $clientIp]
    ]
];
```
**PHP**
```php
<?php
use BellevueRouter\src\Router;

require_once 'routes.php';

$router = new Router($routes);
$router->route('/hi/Yoshi');

function hi($name, $ip) // variables can have any name here.
{
    printf('Hello, %s! Your IP address is %s', $name, $ip);
}
```

**Note:**
> The router does not perform an path variable sanitization. If a variable is specified, every character up to the next forward slash or end of string is captured and passed to your function.

#### Default Route
There is also an optional default route that can be called when no path is given, or when no path is matched.

**Routes**
```php
$clientIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$routes = [
    'default' => [
        'function' => 'home',
    ]
];
```

**PHP**
```php
<?php
use BellevueRouter\src\Router;

require_once 'routes.php';

$router = new Router($routes);
$router->route('unmatched');

function home()
{
    echo 'Welcome home!';
}
```
