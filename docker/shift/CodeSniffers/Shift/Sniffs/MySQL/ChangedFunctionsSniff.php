<?php

namespace Shift\Sniffs\MySQL;

use PHP_CodeSniffer\Util\Tokens;

class ChangedFunctionsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'unpack' => 'unpack',
        'set_error_handler' => 'error_handling',
        'set_exception_handler' => 'error_handling',
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        if (in_array($function, ['set_error_handler', 'set_exception_handler'])) {
            $tokens = $phpcsFile->getTokens();
            $open_parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));
            $argument = $phpcsFile->findNext(Tokens::$emptyTokens, ($open_parenthesis + 1), $tokens[$open_parenthesis]['parenthesis_closer'], true);

            if ($tokens[$argument]['code'] !== T_NULL) {
                return;
            }
        }

        $phpcsFile->addWarning('The function %s() has changed', $stackPtr, $this->forbiddenFunctions[$function], [$function]);
    }
}
