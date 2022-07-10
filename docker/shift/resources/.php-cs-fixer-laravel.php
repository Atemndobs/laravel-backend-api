<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = require 'laravel-ruleset.php';

$project_path = getcwd();
$paths = array_filter([
    $project_path . '/app',
    $project_path . '/config',
    $project_path . '/database',
    $project_path . '/resources',
    $project_path . '/routes',
    $project_path . '/tests',
], function ($path) {
    return is_dir($path);
});

$finder = Finder::create()
    ->in($paths)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
