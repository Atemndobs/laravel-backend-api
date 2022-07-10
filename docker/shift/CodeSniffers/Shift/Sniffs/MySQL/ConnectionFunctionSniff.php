<?php

namespace Shift\Sniffs\MySQL;

class ConnectionFunctionSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'mysql_connect' => 'mysqli_connect',
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

        $arguments = [];
        $argument = $open_parenthesis;

        while ($argument !== false) {
            $arguments[] = $argument;
            $argument = $phpcsFile->findNext(T_COMMA, ($argument + 1), $tokens[$open_parenthesis]['parenthesis_closer']);
        }

        if (count($arguments) == 5) {
            $phpcsFile->addError($error, $stackPtr, $type, $data);
            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $type, $data);
        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenFunctions[$pattern]);

            if (count($arguments) > 3) {
                for ($i = $arguments[3]; $i < $tokens[$open_parenthesis]['parenthesis_closer']; ++$i) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
}
