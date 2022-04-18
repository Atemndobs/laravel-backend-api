<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsRelatedLink
 * 
 * @property int|null $navigation_item_id
 * @property int|null $navigations_items_related_id
 * 
 * @property NavigationsItem|null $navigations_item
 * @property NavigationsItemsRelated|null $navigations_items_related
 *
 * @package App\Models
 */
class NavigationsItemsRelatedLink extends Model
{
	protected $table = 'navigations_items_related_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'navigation_item_id' => 'int',
		'navigations_items_related_id' => 'int'
	];

	protected $fillable = [
		'navigation_item_id',
		'navigations_items_related_id'
	];

	public function navigations_item()
	{
		return $this->belongsTo(NavigationsItem::class, 'navigation_item_id');
	}

	public function navigations_items_related()
	{
		return $this->belongsTo(NavigationsItemsRelated::class);
	}
}
