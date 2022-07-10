<?php

namespace Shift\Sniffs\MySQL;

class CryptFunctionSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'crypt' => 'crypt',
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $tokens = $phpcsFile->getTokens();
        $open_parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));

        $comma = $phpcsFile->findNext(T_COMMA, $open_parenthesis + 1, $tokens[$open_parenthesis]['parenthesis_closer']);
        if ($comma !== false) {
            return;
        }

        $phpcsFile->addWarning('The function %s() has changed', $stackPtr, $function, [$function]);
    }
}
