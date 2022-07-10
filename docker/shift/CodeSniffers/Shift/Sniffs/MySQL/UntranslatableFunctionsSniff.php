<?php

namespace Shift\Sniffs\MySQL;

class UntranslatableFunctionsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'mysql_create_db' => null,
        'mysql_db_name' => null,
        'mysql_drop_db' => null,
        'mysql_field_flags' => null,
        'mysql_field_len' => null,
        'mysql_field_name' => null,
        'mysql_field_table' => null,
        'mysql_field_type' => null,
        'mysql_list_dbs' => null,
        'mysql_list_fields' => null,
        'mysql_list_processes' => null,
        'mysql_list_tables' => null,
        'mysql_result' => null,
        'mysql_tablename' => null,
        'mysql_unbuffered_query' => null,
        'mysql_pconnect' => null,
    ];

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $phpcsFile->addError('The use of function %s() is forbidden', $stackPtr, $function, [$function]);
    }
}
