<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Help
 * 
 * @property int $id
 * @property string|null $content_type
 * @property string|null $path
 * @property string|null $help_content
 * @property string|null $field_name
 * @property string|null $container_type
 * @property string|null $zone_name
 * @property string|null $component_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 *
 * @package App\Models
 */
class Help extends Model
{
	protected $table = 'helps';

	protected $casts = [
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'content_type',
		'path',
		'help_content',
		'field_name',
		'container_type',
		'zone_name',
		'component_name',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}
}
