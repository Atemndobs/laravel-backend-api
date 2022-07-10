<?php

namespace Shift\Sniffs\Laravel;


class AppBindingsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'app' => null,
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $tokens = $phpcsFile->getTokens();
        $open_parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));

        $comma = $phpcsFile->findNext(T_COMMA, $open_parenthesis + 1, $tokens[$open_parenthesis]['parenthesis_closer']);
        if ($comma === false) {
            return;
        }

        $phpcsFile->addError('The function %s() no longer allows bindings', $stackPtr, $function, [$function]);
    }
}
