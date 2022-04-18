<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Extract
 * 
 * @property int $id
 * @property string|null $source
 * @property array|null $data
 * @property string|null $raw_data
 * @property string|null $edited_data
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
class Extract extends Model
{
	protected $table = 'extracts';

	protected $casts = [
		'data' => 'json',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $dates = [
		'published_at'
	];

	protected $fillable = [
		'source',
		'data',
		'raw_data',
		'edited_data',
		'published_at',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}
}
