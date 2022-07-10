<?php

namespace Shift\Sniffs\MySQL;

class ExplicitLinkFunctionsSniff extends \PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff
{
    public $forbiddenFunctions = [
        'mysql_affected_rows' => 'mysqli_affected_rows',
        'mysql_client_encoding' => 'mysqli_character_set_name',
        'mysql_close' => 'mysqli_close',
        'mysql_errno' => 'mysqli_errno',
        'mysql_error' => 'mysqli_error',
        'mysql_escape_string' => 'mysqli_real_escape_string',
        'mysql_get_client_info' => 'mysqli_get_client_info',
        'mysql_get_host_info' => 'mysqli_get_host_info',
        'mysql_get_proto_info' => 'mysqli_get_proto_info',
        'mysql_get_server_info' => 'mysqli_get_server_info',
        'mysql_info' => 'mysqli_info',
        'mysql_insert_id' => 'mysqli_insert_id',
        'mysql_ping' => 'mysqli_ping',
        'mysql_query' => 'mysqli_query',
        'mysql_real_escape_string' => 'mysqli_real_escape_string',
        'mysql_select_db' => 'mysqli_select_db',
        'mysql_set_charset' => 'mysqli_set_charset',
        'mysql_stat' => 'mysqli_stat',
        'mysql_thread_id' => 'mysqli_thread_id',
    ];

    public $specialFunctions = [
        'mysql_escape_string',
        'mysql_query',
        'mysql_real_escape_string',
        'mysql_select_db',
        'mysql_set_charset',
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
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenFunctions[$pattern]);

            $tokens = $phpcsFile->getTokens();
            $open_parenthesis = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

            if (in_array($function, $this->specialFunctions)) {
                $next_token = $phpcsFile->findNext(T_COMMA, ($open_parenthesis + 1), $tokens[$open_parenthesis]['parenthesis_closer']);

                if ($next_token === false) {
                    $phpcsFile->fixer->addContent($open_parenthesis, '$mysqli_link, ');
                } else {
                    $parameter = '';
                    for ($i = $next_token; $i < $tokens[$open_parenthesis]['parenthesis_closer']; ++$i) {
                        $parameter .= $tokens[$i]['content'];
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addContent($open_parenthesis, trim(ltrim($parameter, ',')) . ', ');
                }
            } else {
                $next_token = $phpcsFile->findNext(T_WHITESPACE, ($open_parenthesis + 1), $tokens[$open_parenthesis]['parenthesis_closer'], true);

                if ($next_token === false) {
                    $phpcsFile->fixer->addContent($open_parenthesis, '$mysqli_link');
                }
            }

            $phpcsFile->fixer->endChangeset();
        }
    }
}
