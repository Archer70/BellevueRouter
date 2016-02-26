<?php
namespace Bellevue\Router\test;
use PHPUnit_Framework_TestCase as TestCase;
use Bellevue\Router\test\mocks\RouteSpy;
use Bellevue\Router\src\Router;

class RouterTest extends TestCase
{
    private $router;

    public function setUp()
    {
        $this->router = new Router([
            '/function' => [
                'function' => 'Bellevue\Router\test\fake'
            ],
            '/static' => [
                'class' => 'Bellevue\Router\test\\mocks\RouteSpy',
                'method' => 'main'
            ],
            '/instance' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'mainInstance'
            ],
            'default' => [
                'class' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'main'
            ],
            '/function-with-arguments' => [
                'function' => 'Bellevue\Router\test\fake',
                'arguments' => ['one', 'two']
            ],
            '/class-method-with-arguments' => [
                'class' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'staticArguments',
                'arguments' => ['one', 'two']
            ],
            '/instance-method-with-arguments' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'instanceWithArguments',
                'arguments' => ['one', 'two']
            ],
            '/post/{id}' => [
                'function' => 'Bellevue\Router\test\post',
                'arguments' => ['regular', '{id}',]
            ],
            '/post-one/{first}/post-two/{second}' => [
                'function' => 'Bellevue\Router\test\multiPost',
                'arguments' => ['{first}', '{second}']
            ],
            '/class-args/{arg}/{arg2}' => [
                'class' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'dynamicClassMethod',
                'arguments' => ['{arg}', '{arg2}']
            ],
            '/object-args/{arg}/{arg2}' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'method' => 'dynamicObjectMethod',
                'arguments' => ['{arg}', '{arg2}']
            ],
            '/object-construct/{arg}/{arg2}' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'construct-arguments' => ['{arg}', '{arg2}'],
                'method' => 'mainInstance'
            ],
            '/object-construct-plain-args' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'construct-arguments' => ['nothing interesting', 'plain'],
                'method' => 'mainInstance'
            ],
            '/object-with-deps' => [
                'object' => 'Bellevue\Router\test\mocks\RouteSpy',
                'construct-arguments' => ['nothing interesting', 'plain', 'object:stdClass'],
                'method' => 'mainInstance'
            ]
        ]);
        $this->resetSpyVariables();
    }

    private function resetSpyVariables()
    {
        global $postOne, $postTwo, $functionCalled, $argumentsSet, $givenId;
        RouteSpy::reset();
        $postOne = false;
        $postTwo = false;
        $functionCalled = false;
        $argumentsSet = false;
        $givenId = false;
    }

    public function testRoutesToFunction()
    {
        global $functionCalled;
        $functionCalled = false;
        $this->router->route('/function');
        $this->assertTrue($functionCalled);
    }

    public function testRoutesToStaticClassMethod()
    {
        $this->router->route('/static');
        $this->assertTrue(RouteSpy::$mainCalled);
    }

    public function testRoutesToObjectInstanceMethod()
    {
        $this->router->route('/instance');
        $this->assertTrue(RouteSpy::$mainInstanceCalled);
    }

    public function testRoutesToDefaultIfNoRouteArgument()
    {
        $this->router->route();
        $this->assertTrue(RouteSpy::$mainCalled);
    }

    ////////////////////////////// Argument Tests

    public function testCallFunctionWithStaticArguments()
    {
        global $argumentsSet;
        $this->router->route('/function-with-arguments');
        $this->assertTrue($argumentsSet);
    }

    public function testCallsClassMethodWithStaticArguments()
    {
        $this->router->route('/class-method-with-arguments');
        $this->assertTrue(RouteSpy::$staticArgumentsSet);
    }

    public function testCallsInstanceMethodWithStaticArguments()
    {
        $this->router->route('/instance-method-with-arguments');
        $this->assertTrue(RouteSpy::$instanceArgumentsSet);
    }

    public function testSendsPathVariablesToFunction()
    {
        global $givenId;
        $this->router->route('/post/post-title');
        $this->assertEquals('post-title', $givenId);
    }

    public function testSendsMultipleVariablesToFunction()
    {
        global $postOne, $postTwo;
        $this->router->route('/post-one/first-title/post-two/second-title');
        $this->assertEquals('first-title', $postOne);
        $this->assertEquals('second-title', $postTwo);
    }

    public function testSendsArgumentsToClassMethod()
    {
        $this->router->route('/class-args/arg1/arg2');
        $this->assertTrue(RouteSpy::$dynamicArgumentsSetInStaticMethod);
    }

    public function testSendsArgumentsToObjectMethod()
    {
        $this->router->route('/object-args/arg1/arg2');
        $this->assertTrue(RouteSpy::$dynamicArgumentsSetInObjectMethod);
    }

    public function testSendsArgumentsToObjectConstructor()
    {
        $this->router->route('/object-construct/arg1/arg2');
        $this->assertTrue(RouteSpy::$constructorGotArgs);
    }

    public function testSendsStaticArgsToConstructor()
    {
        $this->router->route('/object-construct-plain-args');
        $this->assertTrue(RouteSpy::$constructorGotArgs);
    }

    public function testRoutesToDefaultIfNoRouteIsMatched()
    {
        $this->router->route('/does-not-exist');
        $this->assertTrue(RouteSpy::$mainCalled);
    }

    // This is the cool stuff ri'tcher
    public function testInjectsDependencyObjects()
    {
        $this->router->route('/object-with-deps');
        $this->assertTrue(RouteSpy::$gotDependencies);
    }

    /**
     * @expectedException Bellevue\Router\src\FileNotFoundException
     * @expectedExceptionMessage Unable to match route, and no default was specified.
     */
    public function testThrowsExceptionIfNoRouteMatchesAndNoDefaultIsSet()
    {
        $router = new Router([]);
        $router->route('/fail');
    }
}

function fake($one = null, $two = null)
{
    global $functionCalled, $argumentsSet;

    $functionCalled = true;
    if (null !== $one && null !== $two) {
        $argumentsSet = true;
    }
}

function post($arg, $id)
{
    global $givenId;
    $givenId = $id;
}

function multiPost($first, $second)
{
    global $postOne, $postTwo;
    $postOne = $first;
    $postTwo = $second;
}
