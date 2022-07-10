<?php

declare(strict_types=1);

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $skip = [
        'vendor/',
        'node_modules/',
    ];

    if (file_exists('.rector-skip')) {
        $skip = array_merge($skip, file('.rector-skip', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    $rectorConfig->skip($skip);
    $rectorConfig->rule(\Rector\Php80\Rector\NotIdentical\StrContainsRector::class);
    $rectorConfig->rule(\Rector\Php80\Rector\Identical\StrStartsWithRector::class);
    $rectorConfig->rule(\Rector\Php80\Rector\Identical\StrEndsWithRector::class);
};
