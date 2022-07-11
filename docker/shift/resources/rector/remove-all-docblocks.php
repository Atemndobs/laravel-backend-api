<?php

declare(strict_types=1);

namespace Shift\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

if (! class_exists(RemoveAllDocblocks::class)) {
    final class RemoveAllDocblocks extends AbstractRector
    {
        public function getNodeTypes(): array
        {
            // https://github.com/rectorphp/php-parser-nodes-docs/
            return [
                ClassConst::class,
                ClassMethod::class,
                Class_::class,
                Const_::class,
                Expression::class,
                Function_::class,
                Interface_::class,
                Property::class,
                Trait_::class,
            ];
        }

        public function refactor(Node $node): ?Node
        {
            // Only remove docblocks, not regular comments
            if ($node->getDocComment()) {
                $node->setAttribute('comments', null);
            }

            return $node;
        }

        public function getRuleDefinition(): RuleDefinition
        {
            return new RuleDefinition('Remove all docblocks', []);
        }
    }
}

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $skip = [
        'vendor/',
        'node_modules/',
        'tests/',
    ];

    if (file_exists('.rector-skip')) {
        $skip = array_merge($skip, file('.rector-skip', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    $rectorConfig->skip($skip);

    $services = $rectorConfig->services();
    $services->set(RemoveAllDocblocks::class);
};
