<?php

namespace Shift\Sniffs\MySQL;

class TranslatableConstantsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    private $constants = [
        'MYSQL_CLIENT_COMPRESS' => 'MYSQLI_CLIENT_COMPRESS',
        'MYSQL_CLIENT_IGNORE_SPACE' => 'MYSQLI_CLIENT_IGNORE_SPACE',
        'MYSQL_CLIENT_INTERACTIVE' => 'MYSQLI_CLIENT_INTERACTIVE',
        'MYSQL_CLIENT_SSL' => 'MYSQLI_CLIENT_SSL',
        'MYSQL_ASSOC' => 'MYSQLI_ASSOC',
        'MYSQL_BOTH' => 'MYSQLI_BOTH',
        'MYSQL_NUM' => 'MYSQLI_NUM',
    ];

    public function register()
    {
        return [T_STRING];
    }

    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $constant = $tokens[$stackPtr]['content'];
        if (! in_array($constant, array_keys($this->constants))) {
            return;
        }

        $fix = $phpcsFile->addFixableError('Use of constant %s is forbidden', $stackPtr, 'ConstantFound', [$constant]);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $this->constants[$constant]);
        }
    }
}
