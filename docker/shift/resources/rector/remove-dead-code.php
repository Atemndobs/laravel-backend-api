<?php

declare(strict_types=1);

use Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\BinaryOp\RemoveDuplicatedInstanceOfRector;
use Rector\DeadCode\Rector\BooleanAnd\RemoveAndTrueRector;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateClassConstantRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDeadConstructorRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveDelegatingParentCallRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\Concat\RemoveConcatAutocastRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Expression\SimplifyMirrorAssignRector;
use Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector;
use Rector\DeadCode\Rector\For_\RemoveDeadLoopRector;
use Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDeadReturnRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveDuplicatedIfReturnRector;
use Rector\DeadCode\Rector\FunctionLike\RemoveOverriddenValuesRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector;
use Rector\DeadCode\Rector\If_\SimplifyIfElseWithSameContentRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfFunctionExistsRector;
use Rector\DeadCode\Rector\MethodCall\RemoveEmptyMethodCallRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\DeadCode\Rector\Plus\RemoveDeadZeroAndOneOperationRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector;
use Rector\DeadCode\Rector\Return_\RemoveDeadConditionAboveReturnRector;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\DeadCode\Rector\Switch_\RemoveDuplicatedCaseInSwitchRector;
use Rector\DeadCode\Rector\TryCatch\RemoveDeadTryCatchRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $skip = [
        'vendor/',
        'node_modules/',
    ];

    if (file_exists('.rector-skip')) {
        $skip = array_merge($skip, file('.rector-skip', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    $rectorConfig->skip($skip);
    $rectorConfig->rule(RemoveDuplicatedArrayKeyRector::class);
    $rectorConfig->rule(RemoveDoubleAssignRector::class);
    $rectorConfig->rule(RemoveUnusedVariableAssignRector::class);
    $rectorConfig->rule(RemoveDuplicatedInstanceOfRector::class);
    $rectorConfig->rule(RemoveAndTrueRector::class);
    $rectorConfig->rule(RecastingRemovalRector::class);
    $rectorConfig->rule(RemoveUnusedPrivateClassConstantRector::class);
    $rectorConfig->rule(RemoveParentCallWithoutParentRector::class);
    $rectorConfig->rule(RemoveDeadConstructorRector::class);
    $rectorConfig->rule(RemoveDelegatingParentCallRector::class);
    $rectorConfig->rule(RemoveEmptyClassMethodRector::class);
    $rectorConfig->rule(RemoveUnusedConstructorParamRector::class);
    $rectorConfig->rule(RemoveUnusedPrivateMethodParameterRector::class);
    $rectorConfig->rule(RemoveUnusedPrivateMethodRector::class);
    $rectorConfig->rule(RemoveConcatAutocastRector::class);
    $rectorConfig->rule(RemoveDeadStmtRector::class);
    $rectorConfig->rule(SimplifyMirrorAssignRector::class);
    $rectorConfig->rule(RemoveDeadIfForeachForRector::class);
    $rectorConfig->rule(RemoveDeadLoopRector::class);
    $rectorConfig->rule(RemoveUnusedForeachKeyRector::class);
    $rectorConfig->rule(RemoveDeadReturnRector::class);
    $rectorConfig->rule(RemoveDuplicatedIfReturnRector::class);
    $rectorConfig->rule(RemoveOverriddenValuesRector::class);
    $rectorConfig->rule(RemoveAlwaysTrueIfConditionRector::class);
    $rectorConfig->rule(RemoveDeadInstanceOfRector::class);
    $rectorConfig->rule(RemoveUnusedNonEmptyArrayBeforeForeachRector::class);
    $rectorConfig->rule(SimplifyIfElseWithSameContentRector::class);
    $rectorConfig->rule(UnwrapFutureCompatibleIfFunctionExistsRector::class);
    $rectorConfig->rule(RemoveEmptyMethodCallRector::class);
    $rectorConfig->rule(RemoveNonExistingVarAnnotationRector::class);
    $rectorConfig->rule(RemoveDeadZeroAndOneOperationRector::class);
    $rectorConfig->rule(RemoveNullPropertyInitializationRector::class);
    $rectorConfig->rule(RemoveUnusedPrivatePropertyRector::class);
    $rectorConfig->rule(RemoveUnusedPromotedPropertyRector::class);
    $rectorConfig->rule(RemoveDeadConditionAboveReturnRector::class);
    $rectorConfig->rule(RemoveUnreachableStatementRector::class);
    $rectorConfig->rule(RemoveDuplicatedCaseInSwitchRector::class);
    $rectorConfig->rule(RemoveDeadTryCatchRector::class);
};
