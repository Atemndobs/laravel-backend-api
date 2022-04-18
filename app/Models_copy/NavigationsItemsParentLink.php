<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsParentLink
 * 
 * @property int|null $navigation_item_id
 * @property int|null $inv_navigation_item_id
 * 
 * @property NavigationsItem|null $navigations_item
 *
 * @package App\Models
 */
class NavigationsItemsParentLink extends Model
{
	protected $table = 'navigations_items_parent_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'navigation_item_id' => 'int',
		'inv_navigation_item_id' => 'int'
	];

	protected $fillable = [
		'navigation_item_id',
		'inv_navigation_item_id'
	];

	public function navigations_item()
	{
		return $this->belongsTo(NavigationsItem::class, 'inv_navigation_item_id');
	}
}
