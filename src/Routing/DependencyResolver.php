<?php


namespace Garaekz\Routing;

use Exception;
use Garaekz\Http\FormRequest;
use ReflectionMethod;

/**
 * Class DependencyResolver
 * 
 * This class provides methods to resolve method dependencies.
 */
class DependencyResolver
{
    /**
     * Resolves the dependencies for a given method in a controller.
     *
     * @param string $controller The fully qualified class name of the controller.
     * @param string $method The name of the method to resolve dependencies for.
     * @param array $params An optional array of parameters to use as dependencies.
     * 
     * @return array An array of resolved dependencies.
     * @throws Exception If a class cannot be resolved.
     */
    public static function resolveMethodDependencies($controller, $method, $params = [])
    {
        try {
            $reflector = new ReflectionMethod($controller, $method);
            $dependencies = [];

            foreach ($reflector->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type && !$type->isBuiltin()) {
                    $className = $type->getName();
                    $dependency = self::resolveClass($className);

                    if (is_subclass_of($dependency, FormRequest::class)) {
                        $dependency->validate();
                    }

                    $dependencies[] = $dependency;
                } else {
                    $dependencies[] = $params[$parameter->getName()] ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
                }
            }

            return $dependencies;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Resolves a class by creating an instance of it.
     *
     * @param string $className The fully qualified class name to resolve.
     * 
     * @return object An instance of the resolved class.
     * @throws Exception If the class cannot be resolved.
     */
    private static function resolveClass($className)
    {
        if (class_exists($className)) {
            return new $className();
        }

        throw new Exception("Unable to resolve class: $className");
    }
}
