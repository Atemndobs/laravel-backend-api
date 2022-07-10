<?php

namespace Shift\Sniffs\Laravel;

class MakeMethodBindingsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    public $forbiddenMethods = [
        'make' => null,
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
            $string .= $name . '();';
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
        if ($tokens[$prevToken]['code'] !== T_OBJECT_OPERATOR && $tokens[$prevToken]['code'] !== T_PAAMAYIM_NEKUDOTAYIM) {
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

        if ($tokens[$prevToken]['code'] === T_PAAMAYIM_NEKUDOTAYIM) {
            $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($prevToken - 1), null, true);
            if (strtolower($tokens[$prevToken]['content']) !== 'app') {
                return;
            }
        }

        $open_parenthesis = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($stackPtr + 1));

        $comma = $phpcsFile->findNext(T_COMMA, $open_parenthesis + 1, $tokens[$open_parenthesis]['parenthesis_closer']);
        if ($comma === false) {
            return;
        }

        $phpcsFile->addError('The function %s() no longer allows bindings', $stackPtr, $function, [$function]);
    }
}
