<?php

namespace Shift\Sniffs\MySQL;

class TranslateMethodsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    public $forbiddenMethods = [
        'settimezoneid' => 'setTimeZone',
    ];

    protected $forbiddenMethodNames = [];

    public function register()
    {
        // Everyone has had a chance to figure out what forbidden functions
        // they want to check for, so now we can cache out the list.
        $this->forbiddenMethodNames = array_keys($this->forbiddenMethods);

        // If we are not pattern matching, we need to work out what
        // tokens to listen for.
        $string = '<?php ';
        foreach ($this->forbiddenMethodNames as $name) {
            $string .= $name.'();';
        }

        $register = [];

        $tokens = token_get_all($string);
        array_shift($tokens);
        foreach ($tokens as $token) {
            if (is_array($token) === true) {
                $register[] = $token[0];
            }
        }

        return array_unique($register);
    }

    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$prevToken]['code'] !== T_OBJECT_OPERATOR) {
            return;
        }

        $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$stackPtr]['code'] === T_STRING && $tokens[$nextToken]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);
        if (in_array($function, $this->forbiddenMethodNames) === false) {
            return;
        }

        $this->addError($phpcsFile, $stackPtr, $function);
    }

    protected function addError($phpcsFile, $stackPtr, $function, $pattern = null)
    {
        $data = [$function];
        $error = 'The use of function %s() is forbidden';
        $type = 'Found';

        if ($pattern === null) {
            $pattern = $function;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $type, $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $this->forbiddenMethods[$pattern]);
        }
    }
}
