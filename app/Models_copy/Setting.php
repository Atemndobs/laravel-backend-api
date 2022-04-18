<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * 
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string|null $value
 * @property string $field
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Setting extends Model
{
	protected $table = 'settings';

	protected $casts = [
		'active' => 'int'
	];

	protected $fillable = [
		'key',
		'name',
		'description',
		'value',
		'field',
		'active'
	];
}
