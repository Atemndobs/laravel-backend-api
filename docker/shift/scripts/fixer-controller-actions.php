<?php

require 'vendor/autoload.php';

echo json_encode(controller_methods());

function controller_methods()
{
    global $argv;

    $controllers = file($argv[1], FILE_IGNORE_NEW_LINES);
    $classes = [];

    foreach ($controllers as $controller) {
        try {
            $class = new ReflectionClass($controller);
        } catch (ReflectionException $e) {
            error_log('could not reflect class: ' . $controller);
            continue;
        }

        $methods = collect($class->getMethods(ReflectionMethod::IS_PUBLIC));

        $classes[$controller] = $methods
            ->filter(function ($method) use ($class) {
                return $method->class == $class->name;
            })
            ->mapWithKeys(function ($method) use ($controller) {
                $parameters = collect($method->getParameters())->mapWithKeys(function (ReflectionParameter $parameter) use ($controller) {
                    $request = null;

                    try {
                        if (is_subclass($parameter, 'Illuminate\\Foundation\\Http\\FormRequest')) {
                            $request = 'Illuminate\\Foundation\\Http\\FormRequest';
                        } elseif (is_class($parameter, 'Illuminate\\Http\\Request') || is_subclass($parameter, 'Illuminate\\Http\\Request')) {
                            $request = 'Illuminate\\Http\\Request';
                        }
                    } catch (ReflectionException $exception) {
                        error_log($exception->getMessage() . ' for ' . $controller);
                    }

                    if ($parameter->hasType()) {
                        if ($parameter->getType() instanceof ReflectionNamedType) {
                            $type = $parameter->getType()->getName();
                        } elseif ($parameter->getType() instanceof ReflectionUnionType) {
                            $type = 'union';
                        }
                    }

                    return [
                        $parameter->getName() => [
                            'type' => $type ?? null,
                            'request' => $request,
                        ],
                    ];
                });

                return [
                    $method->getName() => [
                        'parameters' => $parameters,
                        'startLine' => $method->getStartLine(),
                        'endLine' => $method->getEndLine(),
                    ],
                ];
            });
    }

    return $classes;
}


function is_class(ReflectionParameter $parameter, string $class)
{
    if (is_null($parameter->getType())) {
        return false;
    }

    if (!$parameter->getType() instanceof ReflectionNamedType) {
        return false;
    }

    if ($parameter->getType()->isBuiltin()) {
        return false;
    }

    return $parameter->getType()->getName() === $class;
}

function is_subclass(ReflectionParameter $parameter, string $subclass)
{
    if (is_null($parameter->getType())) {
        return false;
    }

    if (!$parameter->getType() instanceof ReflectionNamedType) {
        return false;
    }

    if ($parameter->getType()->isBuiltin()) {
        return false;
    }

    return is_subclass_of($parameter->getType()->getName(), $subclass);
}
