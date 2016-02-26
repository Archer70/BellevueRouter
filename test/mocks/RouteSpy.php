<?php
namespace Bellevue\Router\test\mocks;

class RouteSpy
{
    public static $mainCalled = false;
    public static $mainInstanceCalled = false;
    public static $staticArgumentsSet = false;
    public static $instanceArgumentsSet = false;
    public static $dynamicArgumentsSetInStaticMethod = false;
    public static $dynamicArgumentsSetInObjectMethod = false;
    public static $constructorGotArgs = false;
    public static $gotDependencies = true;

    public function __construct($arg1 = null, $arg2 = null, $object = null)
    {
        if ($arg1 && $arg2) {
            self::$constructorGotArgs = true;
        }
        if ($object instanceof \stdClass) {
            self::$gotDependencies = true;
        }
    }

    public static function main()
    {
        self::$mainCalled = true;
    }

    public function mainInstance()
    {
        self::$mainInstanceCalled = true;
    }

    public static function staticArguments($one, $two)
    {
        self::$staticArgumentsSet = true;
    }

    public function instanceWithArguments($one, $two)
    {
        self::$instanceArgumentsSet = true;
    }

    public static function dynamicClassMethod($arg1, $arg2)
    {
        self::$dynamicArgumentsSetInStaticMethod = true;
    }

    public function dynamicObjectMethod($arg1, $arg2)
    {
        self::$dynamicArgumentsSetInObjectMethod = true;
    }

    public static function reset()
    {
        self::$mainCalled = false;
        self::$mainInstanceCalled = false;
        self::$staticArgumentsSet = false;
        self::$instanceArgumentsSet = false;
        self::$dynamicArgumentsSetInStaticMethod = false;
        self::$dynamicArgumentsSetInObjectMethod = false;
        self::$constructorGotArgs = false;
        self::$gotDependencies = false;
    }
}
