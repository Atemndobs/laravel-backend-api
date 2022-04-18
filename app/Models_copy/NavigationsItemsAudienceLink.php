<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItemsAudienceLink
 * 
 * @property int|null $navigation_item_id
 * @property int|null $audience_id
 * 
 * @property NavigationsItem|null $navigations_item
 * @property Audience|null $audience
 *
 * @package App\Models
 */
class NavigationsItemsAudienceLink extends Model
{
	protected $table = 'navigations_items_audience_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'navigation_item_id' => 'int',
		'audience_id' => 'int'
	];

	protected $fillable = [
		'navigation_item_id',
		'audience_id'
	];

	public function navigations_item()
	{
		return $this->belongsTo(NavigationsItem::class, 'navigation_item_id');
	}

	public function audience()
	{
		return $this->belongsTo(Audience::class);
	}
}
