<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\CallUserFuncWithArrowFunctionToInlineRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;

return static function (Rector\Config\RectorConfig $rectorConfig): void {
    $skip = [
        'vendor/',
        'node_modules/',
    ];

    if (file_exists('.rector-skip')) {
        $skip = array_merge($skip, file('.rector-skip', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    $rectorConfig->skip($skip);
    $rectorConfig->rule(ClosureToArrowFunctionRector::class);
    $rectorConfig->rule(CallUserFuncWithArrowFunctionToInlineRector::class);
};
