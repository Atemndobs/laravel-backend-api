<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsMasterLink
 * 
 * @property int|null $navigation_item_id
 * @property int|null $navigation_id
 * 
 * @property NavigationsItem|null $navigations_item
 * @property Navigation|null $navigation
 *
 * @package App\Models
 */
class NavigationsItemsMasterLink extends Model
{
	protected $table = 'navigations_items_master_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'navigation_item_id' => 'int',
		'navigation_id' => 'int'
	];

	protected $fillable = [
		'navigation_item_id',
		'navigation_id'
	];

	public function navigations_item()
	{
		return $this->belongsTo(NavigationsItem::class, 'navigation_item_id');
	}

	public function navigation()
	{
		return $this->belongsTo(Navigation::class);
	}
}
