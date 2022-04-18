<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NavigationsItem
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $type
 * @property string|null $path
 * @property string|null $external_path
 * @property string|null $ui_router_key
 * @property bool|null $menu_attached
 * @property int|null $order
 * @property bool|null $collapsed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property Collection|Audience[] $audiences
 * @property NavigationsItemsLink $navigations_items_link
 * @property NavigationsItemsMasterLink $navigations_items_master_link
 * @property NavigationsItemsParentLink $navigations_items_parent_link
 * @property NavigationsItemsRelatedLink $navigations_items_related_link
 *
 * @package App\Models
 */
class NavigationsItem extends Model
{
	protected $table = 'navigations_items';

	protected $casts = [
		'menu_attached' => 'bool',
		'order' => 'int',
		'collapsed' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'title',
		'type',
		'path',
		'external_path',
		'ui_router_key',
		'menu_attached',
		'order',
		'collapsed',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function audiences()
	{
		return $this->belongsToMany(Audience::class, 'navigations_items_audience_links', 'navigation_item_id');
	}

	public function navigations_items_link()
	{
		return $this->hasOne(NavigationsItemsLink::class, 'navigation_item_id');
	}

	public function navigations_items_master_link()
	{
		return $this->hasOne(NavigationsItemsMasterLink::class, 'navigation_item_id');
	}

	public function navigations_items_parent_link()
	{
		return $this->hasOne(NavigationsItemsParentLink::class, 'inv_navigation_item_id');
	}

	public function navigations_items_related_link()
	{
		return $this->hasOne(NavigationsItemsRelatedLink::class, 'navigation_item_id');
	}
}
