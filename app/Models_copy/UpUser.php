<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UpUser
 * 
 * @property int $id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $provider
 * @property string|null $password
 * @property string|null $reset_password_token
 * @property string|null $confirmation_token
 * @property bool|null $confirmed
 * @property bool|null $blocked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property CommentsCommentAuthorUserLink $comments_comment_author_user_link
 * @property UpUsersRoleLink $up_users_role_link
 *
 * @package App\Models
 */
class UpUser extends Model
{
	protected $table = 'up_users';

	protected $casts = [
		'confirmed' => 'bool',
		'blocked' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $hidden = [
		'password',
		'reset_password_token',
		'confirmation_token'
	];

	protected $fillable = [
		'username',
		'email',
		'provider',
		'password',
		'reset_password_token',
		'confirmation_token',
		'confirmed',
		'blocked',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function comments_comment_author_user_link()
	{
		return $this->hasOne(CommentsCommentAuthorUserLink::class, 'user_id');
	}

	public function up_users_role_link()
	{
		return $this->hasOne(UpUsersRoleLink::class, 'user_id');
	}
}
