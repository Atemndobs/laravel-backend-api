<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Card
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property Carbon|null $imported
 * @property array|null $raw_content
 * @property string|null $uuid
 * @property string|null $source
 * @property Carbon|null $updated
 * @property string|null $author
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 *
 * @package App\Models
 */
class Card extends Model
{
	protected $table = 'cards';

	protected $casts = [
		'raw_content' => 'json',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $dates = [
		'imported',
		'updated',
		'published_at'
	];

	protected $fillable = [
		'title',
		'content',
		'imported',
		'raw_content',
		'uuid',
		'source',
		'updated',
		'author',
		'published_at',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}
}
