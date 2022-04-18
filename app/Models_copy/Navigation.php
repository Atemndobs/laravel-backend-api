<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Navigation
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property bool|null $visible
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property NavigationsItemsLink $navigations_items_link
 * @property NavigationsItemsMasterLink $navigations_items_master_link
 *
 * @package App\Models
 */
class Navigation extends Model
{
	protected $table = 'navigations';

	protected $casts = [
		'visible' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'name',
		'slug',
		'visible',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function navigations_items_link()
	{
		return $this->hasOne(NavigationsItemsLink::class);
	}

	public function navigations_items_master_link()
	{
		return $this->hasOne(NavigationsItemsMasterLink::class);
	}
}
