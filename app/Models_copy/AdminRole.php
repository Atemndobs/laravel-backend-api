<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminRole
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property AdminPermissionsRoleLink $admin_permissions_role_link
 * @property AdminUsersRolesLink $admin_users_roles_link
 *
 * @package App\Models
 */
class AdminRole extends Model
{
	protected $table = 'admin_roles';

	protected $casts = [
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'description',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function admin_permissions_role_link()
	{
		return $this->hasOne(AdminPermissionsRoleLink::class, 'role_id');
	}

	public function admin_users_roles_link()
	{
		return $this->hasOne(AdminUsersRolesLink::class, 'role_id');
	}
}
