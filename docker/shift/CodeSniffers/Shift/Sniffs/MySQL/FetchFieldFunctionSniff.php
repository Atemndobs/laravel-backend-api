<?php

namespace Shift\Sniffs\MySQL;

class FetchFieldFunctionSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'mysql_fetch_field' => 'mysqli_fetch_field',
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $data = [$function];
        $error = 'The use of function %s() is forbidden';
        $type = $function;

        if ($pattern === null) {
            $pattern = strtolower($function);
        }

        $tokens = $phpcsFile->getTokens();
        $open_parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));

        $has_second_argument = $phpcsFile->findNext(T_COMMA, ($open_parenthesis + 1), $tokens[$open_parenthesis]['parenthesis_closer']);
        if ($has_second_argument !== false) {
            $phpcsFile->addError($error, $stackPtr, $type, $data);

            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $type, $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenFunctions[$pattern]);
        }
    }
}
