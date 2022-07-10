<?php

require 'vendor/autoload.php';

$models = [];

$files = file($argv[1], FILE_IGNORE_NEW_LINES);

foreach ($files as $file) {
    try {
        $class = new ReflectionClass(class_from_path($file));
    } catch (ReflectionException $e) {
        continue;
    }

    if (!$class->isSubclassOf('Illuminate\\Database\\Eloquent\\Model')) {
        continue;
    }

    $properties = $class->getDefaultProperties();
    if (!isset($properties['table'])) {
        continue;
    }

    $models[$file] = [
        'pivot' => $class->isSubclassOf('Illuminate\\Database\\Eloquent\\Relations\\Pivot'),
        'table' => $properties['table'],
    ];
}


echo json_encode($models);

function class_from_path($path)
{
    return ucfirst(substr(str_replace('/', '\\', $path), 0, -4));
}
