<?php

require 'vendor/autoload.php';

echo json_encode(unguarded_models($argv[1]));

function unguarded_models($file_list)
{
    $models = [];
    $files = file($file_list, FILE_IGNORE_NEW_LINES);

    foreach ($files as $file) {
        try {
            $class = new ReflectionClass(class_from_path($file));
        } catch (ReflectionException $e) {
            error_log('could not reflect class: ' . $file);
            continue;
        }

        if (!$class->isSubclassOf('Illuminate\\Database\\Eloquent\\Model')) {
            continue;
        }

        $properties = $class->getDefaultProperties();
        if (isset($properties['guarded']) && $properties['guarded'] !== ['*']) {
            continue;
        }

        $models[] = $file;
    }

    return $models;
}

function class_from_path($path)
{
    return ucfirst(substr(str_replace('/', '\\', $path), 0, -4));
}
