<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = require 'laravel-ruleset.php';

$project_path = getcwd();
$finder = Finder::create()
    ->in([$project_path . '/app'])
    ->name('*.php.*')
    ->notName('*.blade.php.*')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config)
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
