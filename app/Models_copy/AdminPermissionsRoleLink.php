<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminPermissionsRoleLink
 * 
 * @property int|null $permission_id
 * @property int|null $role_id
 * 
 * @property AdminPermission|null $admin_permission
 * @property AdminRole|null $admin_role
 *
 * @package App\Models
 */
class AdminPermissionsRoleLink extends Model
{
	protected $table = 'admin_permissions_role_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'permission_id' => 'int',
		'role_id' => 'int'
	];

	protected $fillable = [
		'permission_id',
		'role_id'
	];

	public function admin_permission()
	{
		return $this->belongsTo(AdminPermission::class, 'permission_id');
	}

	public function admin_role()
	{
		return $this->belongsTo(AdminRole::class, 'role_id');
	}
}
