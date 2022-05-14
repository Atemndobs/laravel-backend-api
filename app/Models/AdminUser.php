<?php

namespace App\Models;

use App\Models\Base\AdminUser as BaseAdminUser;

class AdminUser extends BaseAdminUser
{
	protected $hidden = [
		'password',
		'reset_password_token',
		'registration_token'
	];

	protected $fillable = [
		'firstname',
		'lastname',
		'username',
		'email',
		'password',
		'reset_password_token',
		'registration_token',
		'is_active',
		'blocked',
		'prefered_language',
		'created_by_id',
		'updated_by_id'
	];
}
