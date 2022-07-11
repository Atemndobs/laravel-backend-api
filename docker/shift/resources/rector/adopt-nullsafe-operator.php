<?php

declare(strict_types=1);

namespace Shift\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Ternary;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

if (! class_exists(ShiftNullsafeOperatorRector::class)) {
    final class ShiftNullsafeOperatorRector extends AbstractRector
    {
        /**
         * @return array<class-string<Node>>
         */
        public function getNodeTypes(): array
        {
            return [Ternary::class];
        }

        public function getRuleDefinition(): RuleDefinition
        {
            return new RuleDefinition('Convert ternary to nullsafe operator', []);
        }

        public function refactor(Node $node): ?Node
        {
            if ($this->shouldSkipTernary($node)) {
                return null;
            }

            $nullSafeElse = $this->processNullSafeExpr($node->else);

            if ($nullSafeElse !== null) {
                return $nullSafeElse;
            }

            if ($node->if === null) {
                return null;
            }

            return $this->processNullSafeExpr($node->if);
        }

        private function shouldSkipTernary(Ternary $ternary): bool
        {
            if (! $this->canTernaryReturnNull($ternary)) {
                return true;
            }

            if (! $ternary->cond instanceof Identical && ! $ternary->cond instanceof NotIdentical) {
                return true;
            }

            if (! $this->hasNullComparison($ternary->cond)) {
                return true;
            }

            return $this->hasIndirectUsageOnElse($ternary->cond, $ternary->if, $ternary->else);
        }

        private function hasIndirectUsageOnElse(Identical|NotIdentical $cond, ?Expr $if, Expr $expr): bool
        {
            $left = $cond->left;
            $right = $cond->right;

            $object = $this->valueResolver->isNull($left)
                ? $right
                : $left;

            if ($this->valueResolver->isNull($expr)) {
                if ($this->isMethodCallOrPropertyFetch($if)) {
                    /** @var MethodCall|PropertyFetch $if */
                    return ! $this->nodeComparator->areNodesEqual($if->var, $object);
                }

                return false;
            }

            if ($this->isMethodCallOrPropertyFetch($expr)) {
                /** @var MethodCall|PropertyFetch $expr */
                return ! $this->nodeComparator->areNodesEqual($expr->var, $object);
            }

            return false;
        }

        private function isMethodCallOrPropertyFetch(?Expr $expr): bool
        {
            return $expr instanceof MethodCall || $expr instanceof PropertyFetch;
        }

        private function hasNullComparison(NotIdentical|Identical $check): bool
        {
            if ($this->valueResolver->isNull($check->left)) {
                return true;
            }

            return $this->valueResolver->isNull($check->right);
        }

        private function canTernaryReturnNull(Ternary $ternary): bool
        {
            if ($this->valueResolver->isNull($ternary->else)) {
                return true;
            }

            if ($ternary->if === null) {
                // $foo === null ?: 'xx' returns true if $foo is null
                // therefore it does not return null in case of the elvis operator
                return false;
            }

            return $this->valueResolver->isNull($ternary->if);
        }

        private function processNullSafeExpr(Expr $expr): NullsafeMethodCall|NullsafePropertyFetch|null
        {
            if ($expr instanceof MethodCall) {
                return new NullsafeMethodCall($expr->var, $expr->name, $expr->args);
            }

            if ($expr instanceof PropertyFetch) {
                return new NullsafePropertyFetch($expr->var, $expr->name);
            }

            return null;
        }
    }
}

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $skip = [
        'vendor/',
        'node_modules/',
    ];

    if (file_exists('.rector-skip')) {
        $skip = array_merge($skip, file('.rector-skip', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    $rectorConfig->skip($skip);

    $services = $rectorConfig->services();
    $services->set(ShiftNullsafeOperatorRector::class);
};
