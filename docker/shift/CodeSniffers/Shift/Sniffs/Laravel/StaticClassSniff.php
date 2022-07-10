<?php

namespace Shift\Sniffs\Laravel;

use PHP_CodeSniffer\Config;

class StaticClassSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    private $namespaces = ['Illuminate'];

    public function register()
    {
        $namespace = Config::getConfigData('laravel_app_namespace');
        if (!empty($namespace)) {
            $this->namespaces[] = $namespace;
        }

        return [T_CONSTANT_ENCAPSED_STRING];
    }

    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $string = trim($tokens[$stackPtr]['content'], '\'"');

        if (!preg_match('/^[a-zA-Z0-9_\x7f-\xff\\\\]+$/', $string)) {
            return;
        }

        $parts = explode('\\', trim($string, '\\'));
        if (count($parts) < 2 || !in_array($parts[0], $this->namespaces)) {
            return;
        }

        array_shift($parts);
        $path = 'app/' . implode('/', $parts) . '.php';

        if (!file_exists($path)) {
            return;
        }

        $string = preg_replace('/\\\\{2,}/', '\\', $string);

        if (strpos($phpcsFile->getFilename(), '/config/') === false) {
            $string = '\\' . trim($string, '\\');
        }

        $fix = $phpcsFile->addFixableError('Use ::class property to reference classes instead of a string', $stackPtr, $string);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $string . '::class');
        }
    }
}
