<?php

namespace Shift\Sniffs\MySQL;

class TranslateFunctionsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'datefmt_set_timezone_id' => 'datefmt_set_timezone',
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $data = [$function];
        $error = 'The use of function %s() is forbidden';
        $type = 'Found';

        if ($pattern === null) {
            $pattern = strtolower($function);
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $type, $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenFunctions[$pattern]);
        }
    }
}
