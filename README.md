# BellevueRouter
A small router with path variable support.

## Example

```php
<?php
use Bellevue\Router\src\Router;

require_once('vendor/autoload.php');

// I'd recommend putting this in a JSON or YAML file.
$routes = [
    'default' => [
        'function' => 'home'
    ],
        '/hi/{name}' => [
        'class' => 'Hi',
        'method' => 'welcome',
        'arguments' => ['{name}']
    ],
        '/some-path' => [
        'object' => 'Hi',
        'method' => 'instanceMethod',
        'arguments' => ['value1', 'value2']
    ]
];

$path = !empty($_GET['p']) ? $_GET['p'] : ''; // index.php?p=/hi/Yoshi

$router = new Router($routes);
$router->route($path);


function home()
{
    echo 'hey';
}

class Hi
{
    public static function welcome($name)
    {
        echo 'Hello there, ' . $name;
    }

    public function instanceMethod($var1, $var2)
    {
        echo $var1;
        echo $var2;
    }
}
```