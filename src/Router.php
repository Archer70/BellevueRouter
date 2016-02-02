<?php
namespace Bellevue\Router\src;

class Router
{
    private $routes;
    private $path;
    private $pathVariableData;
    private $matchedRoute;
    private $matchedRoutePath;
    private $executor;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
        $this->executor = new Executor();
    }

    public function route($path = null)
    {
        $this->path = $path;
        $this->matchedRoute = $this->getMatchedRoute();

        $this->executor->execute($this->matchedRoute, $this->functionArguments());
    }

    private function getMatchedRoute()
    {
        if (array_key_exists($this->path, $this->routes)) {
            return $this->routes[$this->path];
        } else if (null !== $this->getRouteWithVariables()) {
            return $this->getRouteWithVariables();
        } else if (array_key_exists('default', $this->routes)) {
            return $this->routes['default'];
        } else {
            throw new FileNotFoundException('Unable to match route, and no default was specified.');
        }
    }

    private function getRouteWithVariables()
    {
        foreach ($this->routes as $route => $info) {
            if (preg_match($this->routeRegex($route), $this->path)) {
                $this->assignPathVariables($route);
                return $this->routes[$route];
            }
        }
    }

    private function assignPathVariables($route)
    {
        preg_match($this->routeRegex($route), $this->path, $this->pathVariableData);
        array_shift($this->pathVariableData);
        $this->matchedRoutePath = $route;
    }

    private function routeRegex($route)
    {
        $regex = str_replace('/', '\/', $route);
        return '/' . preg_replace('/\{.+\}/U', '([a-zA-Z0-9_\-\+]+)', $regex) . '$/';
    }

    private function functionArguments()
    {
        $arguments = array_key_exists('arguments', $this->matchedRoute) ? $this->matchedRoute['arguments'] : [];
        $variablesInRoute = $this->getVariablesInRoute();
        if (!empty($variablesInRoute)) {
            return $this->replaceVariableNamesWithValues($arguments);
        }
        return $arguments;
    }

    private function getVariablesInRoute()
    {
        preg_match_all('/(\{.+\})/U', $this->matchedRoutePath, $variables);
        $variables = $variables[1];
        return $variables;
    }

    private function replaceVariableNamesWithValues($arguments)
    {
        foreach ($arguments as $argumentKey => $argument) {
            foreach ($this->getPathVariablesByNamedKey() as $pathVariableName => $pathVariable) {
                if ($pathVariableName === $argument) {
                    $arguments[$argumentKey] = $pathVariable;
                }
            }
        }
        return $arguments;
    }

    private function getPathVariablesByNamedKey()
    {
        return array_combine($this->getVariablesInRoute(), $this->pathVariableData);
    }
}