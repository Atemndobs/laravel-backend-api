<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsLink
 * 
 * @property int|null $navigation_id
 * @property int|null $navigation_item_id
 * 
 * @property Navigation|null $navigation
 * @property NavigationsItem|null $navigations_item
 *
 * @package App\Models
 */
class NavigationsItemsLink extends Model
{
	protected $table = 'navigations_items_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'navigation_id' => 'int',
		'navigation_item_id' => 'int'
	];

	protected $fillable = [
		'navigation_id',
		'navigation_item_id'
	];

	public function navigation()
	{
		return $this->belongsTo(Navigation::class);
	}

	public function navigations_item()
	{
		return $this->belongsTo(NavigationsItem::class, 'navigation_item_id');
	}
}
