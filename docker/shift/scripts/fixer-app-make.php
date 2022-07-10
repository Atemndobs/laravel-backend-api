<?php

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

require 'vendor/autoload.php';

// helpers...
function class_from_path($path)
{
    return ucfirst(substr(str_replace('/', '\\', $path), 0, -4));
}

$parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
$printer = new Standard();

// Replace:
// (App::|app->)make\(([^,)]+?, .+?)\);
// $1makeWith($2);

// Generate list of files with...
// grep -lFr 'makeWith(' app > make.list
// TODO: do not redo already keyed parameters

$files = file($argv[1], FILE_IGNORE_NEW_LINES);
$classes = [];

foreach ($files as $path) {
    $contents = file_get_contents(getcwd() . '/' . $path);

    $matches = [];
    $count = preg_match_all('/(App::|app->)makeWith\(([^,]+),\s*(.+?)\);/', $contents, $matches, PREG_SET_ORDER);

    if (!$count) {
        continue;
    }

    foreach ($matches as $match) {
        if (strpos($match[2], '::class') === false) {
            continue;
        }

        $class_name = substr($match[2], 0, -7);

        $imports = [];
        $found = preg_match('/^use\s*(.+?\\\\' . $class_name . ');$/m', $contents, $imports);

        if ($found) {
            $class_name = $imports[1];
        }

        try {
            $class = new ReflectionClass($class_name);
        } catch (ReflectionException $e) {
            echo 'Unable to reflect class: ', $class_name, PHP_EOL, $e->getMessage(), PHP_EOL, PHP_EOL;
            continue;
        }

        $names = collect($class->getConstructor()->getParameters())
            ->mapWithKeys(function (ReflectionParameter $parameter) {
                return [$parameter->getName() => $parameter->isDefaultValueAvailable()];
            });

        try {
            $ast = $parser->parse('<?php ' . $match[3] . ';');
        } catch (PhpParser\Error $e) {
            echo 'Unable to parse: ', $match[3], PHP_EOL, ' in: ', $path, PHP_EOL, PHP_EOL;
            continue;
        }

        $elements = [];
        foreach ($ast[0]->expr->items as $element) {
            $elements[] = $printer->prettyPrintExpr($element);
        }

        $values = $names->keys()
            ->take(count($elements))
            ->combine($elements)
            ->map(function ($item, $key) {
                return "'${key}' => ${item}";
            })
            ->all();

        $contents = str_replace($match[0], $match[1] . 'makeWith(' . $match[2] . ', [' . implode(', ', $values) . ']);', $contents);
    }

    file_put_contents($path, $contents);
}
