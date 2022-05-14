<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * Class Feed
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $payload
 * @property string $source
 * @property string $image
 * @property string $since
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Feed extends \App\Models\Base\Feed
{
    use CrudTrait;
    use Notifiable;
    use HasRoles;

	protected $table = 'feeds';

	protected $fillable = [
		'title',
		'content',
		'payload',
		'source',
	];
}
