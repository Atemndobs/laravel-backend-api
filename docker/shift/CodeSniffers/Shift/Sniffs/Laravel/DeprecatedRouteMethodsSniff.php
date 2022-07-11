<?php

namespace Shift\Sniffs\Laravel;

class DeprecatedRouteMethodsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'get' => null,
        'post' => null,
        'put' => null,
        'delete' => null,
    ];
}
