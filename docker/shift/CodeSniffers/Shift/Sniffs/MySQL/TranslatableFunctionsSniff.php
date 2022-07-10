<?php

namespace Shift\Sniffs\MySQL;

class TranslatableFunctionsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'mysql_data_seek' => 'mysqli_data_seek',
        'mysql_fetch_array' => 'mysqli_fetch_array',
        'mysql_fetch_assoc' => 'mysqli_fetch_assoc',
        'mysql_fetch_lengths' => 'mysqli_fetch_lengths',
        'mysql_fetch_object' => 'mysqli_fetch_object',
        'mysql_fetch_row' => 'mysqli_fetch_row',
        'mysql_field_seek' => 'mysqli_field_seek',
        'mysql_free_result' => 'mysqli_free_result',
        'mysql_num_fields' => 'mysqli_num_fields',
        'mysql_num_rows' => 'mysqli_num_rows',
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
