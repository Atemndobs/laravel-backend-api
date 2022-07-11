<?php

require 'vendor/autoload.php';

$classes = array_merge(controller_methods(), middleware_methods());
echo json_encode($classes);

// helpers...
function class_from_path($path)
{
    return ucfirst(substr(str_replace('/', '\\', $path), 0, -4));
}

function controller_methods()
{
    global $argv;

    $files = file($argv[1], FILE_IGNORE_NEW_LINES);
    $classes = [];

    foreach ($files as $file) {
        try {
            $class = new ReflectionClass(class_from_path($file));
        } catch (ReflectionException $e) {
            error_log('could not reflect class: '.class_from_path($file).' in: '.$file);
            continue;
        }

        $methods = collect($class->getMethods(ReflectionMethod::IS_PUBLIC));

        $classes[$file] = $methods
            ->reject(function ($method) {
                return $method->isStatic();
            })
            ->filter(function ($method) use ($class) {
                return $method->class == $class->name;
            })
            ->mapWithKeys(function (ReflectionMethod $method) use ($file) {
                $request_parameter = collect($method->getParameters())->first(function (ReflectionParameter $parameter) use ($file) {
                    try {
                        $is_request = is_request_parameter($parameter);
                    } catch (ReflectionException $exception) {
                        error_log($exception->getMessage().' for '.$file);
                        $is_request = false;
                    }

                    return $is_request;
                });

                return [
                    $method->getName() => [
                        'requestParameter' => $request_parameter ? $request_parameter->getName() : null,
                        'isFormRequest' => is_form_request_parameter($request_parameter),
                        'parameterCount' => count($method->getParameters()),
                        'startLine' => $method->getStartLine(),
                        'endLine' => $method->getEndLine(),
                    ],
                ];
            });
    }

    return $classes;
}

function middleware_methods()
{
    global $argv;

    if (! isset($argv[2])) {
        return [];
    }

    $files = file($argv[2], FILE_IGNORE_NEW_LINES);
    $classes = [];

    foreach ($files as $file) {
        try {
            $class = new ReflectionClass(class_from_path($file));
        } catch (ReflectionException $e) {
            continue;
        }

        if (! $class->hasMethod('handle')) {
            continue;
        }
        $method = $class->getMethod('handle');

        if ($method->class !== $class->name) {
            continue;
        }

        $classes[$file] = [
            $method->getName() => [
                'requestParameter' => $method->getParameters()[0]->getName(),
                'parameterCount' => count($method->getParameters()),
                'startLine' => $method->getStartLine(),
                'endLine' => $method->getEndLine(),
            ],
        ];
    }

    return $classes;
}

function is_request_parameter(ReflectionParameter $parameter)
{
    if (is_null($parameter->getType())) {
        return false;
    }

    if (! $parameter->getType() instanceof ReflectionNamedType) {
        return false;
    }

    if ($parameter->getType()->isBuiltin()) {
        return false;
    }

    if (in_array($parameter->getType()->getName(), ['Illuminate\\Http\\Request', 'Illuminate\\Support\\Facades\\Request'])) {
        return true;
    }

    if (is_subclass_of($parameter->getType()->getName(), 'Illuminate\\Http\\Request')) {
        return true;
    }

    return false;
}

function is_form_request_parameter(?ReflectionParameter $parameter)
{
    if (is_null($parameter?->getType())) {
        return false;
    }

    if (! $parameter->getType() instanceof ReflectionNamedType) {
        return false;
    }

    if ($parameter->getType()->isBuiltin()) {
        return false;
    }

    return is_subclass_of($parameter->getType()->getName(), 'Illuminate\\Foundation\\Http\\FormRequest');
}
