<?php
namespace Bellevue\Router\src;

class Executor
{
    private $matchedRoute;
    private $functionArguments;
    private $constructorArguments;

    public function execute(array $matchedRoute, array $functionArguments = null, array $constructorArguments = null)
    {
        $this->matchedRoute = $matchedRoute;
        $this->functionArguments = $functionArguments;
        $this->constructorArguments = $constructorArguments;

        $this->runBasedOnType();
    }

    private function runBasedOnType()
    {
        if (array_key_exists('function', $this->matchedRoute)) {
            $this->callFunction();
        } else if (array_key_exists('class', $this->matchedRoute)) {
            $this->callStaticMethod();
        } else if (array_key_exists('object', $this->matchedRoute)) {
            $this->callObjectMethod();
        }
    }

    private function callFunction()
    {
        call_user_func_array($this->matchedRoute['function'], $this->functionArguments);
    }

    private function callStaticMethod()
    {
        call_user_func_array($this->matchedRoute['class'] . '::' . $this->matchedRoute['method'], $this->functionArguments);
    }

    private function callObjectMethod()
    {
        if ($this->constructorArguments) {
            $reflection = new \ReflectionClass($this->matchedRoute['object']);
            $object = $reflection->newInstanceArgs($this->constructorArguments);
        } else {
            $object = new $this->matchedRoute['object'];
        }
        call_user_func_array([$object, $this->matchedRoute['method']], $this->functionArguments);
    }
}
