<?php

declare(strict_types=1);

namespace Shift\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

if (!class_exists(RemoveLaravelDocblocks::class)) {
    final class RemoveLaravelDocblocks extends AbstractRector
    {
        private $methodComments = [
            // Shared
            '__construct' => [
                'Create a new command instance',
                'Create a new event instance',
                'Create the event listener',
                'Create a new job instance',
                'Create a new message instance',
                'Create a new policy instance',
                'Create a new rule instance',
            ],
            'create' => [
                'Show the form for creating a new resource',
                'Determine whether the user can create',
            ],
            'failed' => ['Handle a job failure'],
            'handle' => [
                'Execute the console command',
                'Execute the job',
                'Handle the event',
                'Handle an incoming request',
            ],
            'retryUntil' => [
                'Determine the time at which the listener should timeout',
                'Determine the time at which the job should timeout',
            ],
            'update' => [
                'Update the specified resource in storage',
                'Determine whether the user can update',
            ],

            // Controller
            'index' => ['Display a listing of the resource'],
            'store' => ['Store a newly created resource in storage'],
            'show' => ['Display the specified resource'],
            'edit' => ['Show the form for editing the specified resource'],
            'destroy' => ['Remove the specified resource from storage'],

            // Event
            'broadcastOn' => ['Get the channels the event should broadcast on'],

            // Factory
            'definition' => ['Define the model\'s default state'],

            // Job
            'backoff' => ['Calculate the number of seconds to wait before retrying the job'],
            'uniqueId' => ['The unique ID of the job'],
            'uniqueVia' => ['Get the cache driver for the unique job lock'],

            // Listener
            'queue' => ['Get the name of the listener\'s queue'],
            'shouldQueue' => ['Determine whether the listener should be queued'],

            // Mail
            'build' => ['Build the message'],

            // Migration
            'down' => ['Reverse the migration'],
            'up' => ['Run the migration'],

            // Model
            'booted' => [
                'Perform any actions required after the model boots',
                'The "booted" method of the model',
            ],
            'getRouteKeyName' => ['Get the route key for the model'],
            'newCollection' => ['Create a new Eloquent Collection instance'],
            'serializeDate' => ['Prepare a date for array / JSON serialization'],

            // Policy
            'viewAny' => ['Determine whether the user can view'],
            'view' => ['Determine whether the user can view'],
            'delete' => ['Determine whether the user can delete'],
            'restore' => ['Determine whether the user can restore the'],
            'forceDelete' => ['Determine whether the user can permanently delete'],

            // Request
            'authorize' => ['Determine if the user is authorized to make this request'],
            'rules' => ['Get the validation rules that apply to the request'],
            'messages' => [
                'Get custom messages for validator errors',
                'Get the error messages for the defined validation rules',
            ],

            // Rule
            'message' => ['Get the validation error message'],
            'passes' => ['Determine if the validation rule passes'],

            // Seeder
            'run' => ['Run the database seeds'],
        ];

        private $propertyComments = [
            // Shared
            'connection' => [
                'The connection name for the model',
                'The database connection that should be used by the model',
                'The name of the connection the job should be sent to',
            ],
            'delay' => [
                'The time (seconds) before the job should be processed',
                'The number of seconds before the job should be made available',
            ],
            'tries' => [
                'The number of times the queued listener may be attempted',
                'The number of times the job may be attempted',
            ],
            'queue' => [
                'The name of the queue the job should be sent to',
            ],

            // Command
            'signature' => ['The name and signature of the console command'],
            'description' => ['The console command description'],

            // Factory
            'model' => ['The name of the factory\'s corresponding model'],

            // Job
            'backoff' => ['The number of seconds to wait before retrying the job'],
            'chainConnection' => ['The name of the connection the chain should be sent to'],
            'chainQueue' => ['The name of the queue the chain should be sent to'],
            'chainCatchCallbacks' => ['The callbacks to be executed on chain failure'],
            'uniqueFor' => ['The number of seconds after which the job\'s unique lock will be released'],
            'maxExceptions' => ['The maximum number of unhandled exceptions to allow before failing'],
            'timeout' => ['The number of seconds the job can run before timing out'],

            // Model
            'appends' => ['The accessors to append to the model\'s array form'],
            'attributes' => [
                'The model\'s attributes',
                'The model\'s default values for attributes',
            ],
            'casts' => ['The attributes that should be cast'],
            'dateFormat' => ['The storage format of the model\'s date columns'],
            'dates' => ['The attributes that should be mutated to dates'],
            'dispatchesEvents' => ['The event map for the model'],
            'fillable' => ['The attributes that are mass assignable'],
            'guarded' => ['The attributes that aren\'t mass assignable'],
            'hidden' => [
                'The attributes that should be hidden for arrays',
                'The attributes that should be hidden for serialization',
                'The attributes excluded from the model\'s JSON form',
            ],
            'incrementing' => [
                'Indicates if the IDs are auto-incrementing',
                'Indicates if the model\'s ID is auto-incrementing',
            ],
            'keyType' => [
                'The "type" of the primary key ID',
                'The data type of the auto-incrementing ID.',
            ],
            'observables' => ['User exposed observable events'],
            'perPage' => ['The number of models to return for pagination'],
            'preventsLazyLoading' => ['Indicates whether lazy loading will be prevented on this model'],
            'primaryKey' => [
                'The primary key for the model',
                'The primary key associated with the table',
            ],
            'snakeAttributes' => ['Indicates whether attributes are snake cased on arrays'],
            'table' => ['The table associated with the model'],
            'timestamps' => ['Indicates if the model should be timestamped'],
            'touches' => [
                'All of the relationships to be touched',
                'The relationships that should be touched on save',
            ],
            'visible' => [
                'The attributes that should be visible in arrays',
                'The attributes that should be visible in serialization',
            ],
            'with' => [
                'The relations to eager load on every query',
                'The relationships that should always be loaded',
            ],
            'withCount' => ['The relationship counts that should be eager loaded on every query'],
        ];

        private $constantComments = [
            'CREATED_AT' => ['The name of the "created at" column'],
            'UPDATED_AT' => ['The name of the "updated at" column'],
        ];

        public function getNodeTypes(): array
        {
            return [
                ClassConst::class,
                ClassMethod::class,
                Property::class,
            ];
        }

        public function refactor(Node $node): ?Node
        {
            // This could be handy if we need to determine the type of file (based on path/name)
            // $this->file->getSmartFileInfo()->getRealPath()

            if (!$node->getDocComment()) {
                return $node;
            }

            if ($node instanceof ClassMethod) {
                return $this->refactorMethod($node);
            }

            if ($node instanceof Property) {
                return $this->refactorProperty($node);
            }

            if ($node instanceof ClassConst) {
                return $this->refactorConstant($node);
            }

            return $node;
        }

        public function getRuleDefinition(): RuleDefinition
        {
            return new RuleDefinition('Remove default Laravel docblocks', []);
        }

        private function refactorMethod(ClassMethod $method): ClassMethod
        {
            if (!isset($this->methodComments[$method->name->name])) {
                return $method;
            }

            return $this->stripCommentsFromNode($this->methodComments[$method->name->name], $method);
        }

        private function refactorProperty(Property $property): Property
        {
            if (!isset($this->propertyComments[$property->props[0]->name->name])) {
                return $property;
            }

            return $this->stripCommentsFromNode($this->propertyComments[$property->props[0]->name->name], $property);
        }

        private function refactorConstant(ClassConst $constant): ClassConst
        {
            if (!isset($this->constantComments[$constant->consts[0]->name->name])) {
                return $constant;
            }

            return $this->stripCommentsFromNode($this->constantComments[$constant->consts[0]->name->name], $constant);
        }

        private function stripCommentsFromNode(array $comments, Node $node): Node
        {
            foreach ($comments as $comment) {
                if (str_contains($node->getDocComment()->getText(), $comment)) {
                    $node->setAttribute('comments', null);

                    return $node;
                }
            }

            return $node;
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
    $services->set(RemoveLaravelDocblocks::class);
};
