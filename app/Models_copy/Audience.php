<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Audience
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property Collection|NavigationsItem[] $navigations_items
 *
 * @package App\Models
 */
class Audience extends Model
{
	protected $table = 'audience';

	protected $casts = [
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'name',
		'key',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function navigations_items()
	{
		return $this->belongsToMany(NavigationsItem::class, 'navigations_items_audience_links', 'audience_id', 'navigation_item_id');
	}
}
