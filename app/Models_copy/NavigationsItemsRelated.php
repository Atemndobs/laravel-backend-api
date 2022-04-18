<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsRelated
 * 
 * @property int $id
 * @property string|null $related_id
 * @property string|null $related_type
 * @property string|null $field
 * @property int|null $order
 * @property string|null $master
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property NavigationsItemsRelatedLink $navigations_items_related_link
 *
 * @package App\Models
 */
class NavigationsItemsRelated extends Model
{
	protected $table = 'navigations_items_related';

	protected $casts = [
		'order' => 'int',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'related_id',
		'related_type',
		'field',
		'order',
		'master',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function navigations_items_related_link()
	{
		return $this->hasOne(NavigationsItemsRelatedLink::class);
	}
}
