<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminUser
 * 
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $username
 * @property string|null $email
 * @property string|null $password
 * @property string|null $reset_password_token
 * @property string|null $registration_token
 * @property bool|null $is_active
 * @property bool|null $blocked
 * @property string|null $prefered_language
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property Collection|AdminPermission[] $admin_permissions
 * @property Collection|AdminRole[] $admin_roles
 * @property Collection|AdminUser[] $admin_users
 * @property AdminUsersRolesLink $admin_users_roles_link
 * @property Collection|Audience[] $audiences
 * @property Collection|Card[] $cards
 * @property Collection|CommentsComment[] $comments_comments
 * @property Collection|CommentsCommentReport[] $comments_comment_reports
 * @property Collection|Extract[] $extracts
 * @property Collection|File[] $files
 * @property Collection|Help[] $helps
 * @property Collection|I18nLocale[] $i18n_locales
 * @property Collection|Navigation[] $navigations
 * @property Collection|NavigationsItem[] $navigations_items
 * @property Collection|NavigationsItemsRelated[] $navigations_items_relateds
 * @property Collection|Song[] $songs
 * @property Collection|StrapiApiToken[] $strapi_api_tokens
 * @property Collection|UpPermission[] $up_permissions
 * @property Collection|UpRole[] $up_roles
 * @property Collection|UpUser[] $up_users
 *
 * @package App\Models
 */
class AdminUser extends Model
{
	protected $table = 'admin_users';

	protected $casts = [
		'is_active' => 'bool',
		'blocked' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

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

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function admin_permissions()
	{
		return $this->hasMany(AdminPermission::class, 'updated_by_id');
	}

	public function admin_roles()
	{
		return $this->hasMany(AdminRole::class, 'updated_by_id');
	}

	public function admin_users()
	{
		return $this->hasMany(AdminUser::class, 'updated_by_id');
	}

	public function admin_users_roles_link()
	{
		return $this->hasOne(AdminUsersRolesLink::class, 'user_id');
	}

	public function audiences()
	{
		return $this->hasMany(Audience::class, 'updated_by_id');
	}

	public function cards()
	{
		return $this->hasMany(Card::class, 'updated_by_id');
	}

	public function comments_comments()
	{
		return $this->hasMany(CommentsComment::class, 'updated_by_id');
	}

	public function comments_comment_reports()
	{
		return $this->hasMany(CommentsCommentReport::class, 'updated_by_id');
	}

	public function extracts()
	{
		return $this->hasMany(Extract::class, 'updated_by_id');
	}

	public function files()
	{
		return $this->hasMany(File::class, 'updated_by_id');
	}

	public function helps()
	{
		return $this->hasMany(Help::class, 'updated_by_id');
	}

	public function i18n_locales()
	{
		return $this->hasMany(I18nLocale::class, 'updated_by_id');
	}

	public function navigations()
	{
		return $this->hasMany(Navigation::class, 'updated_by_id');
	}

	public function navigations_items()
	{
		return $this->hasMany(NavigationsItem::class, 'updated_by_id');
	}

	public function navigations_items_relateds()
	{
		return $this->hasMany(NavigationsItemsRelated::class, 'updated_by_id');
	}

	public function songs()
	{
		return $this->hasMany(Song::class, 'updated_by_id');
	}

	public function strapi_api_tokens()
	{
		return $this->hasMany(StrapiApiToken::class, 'updated_by_id');
	}

	public function up_permissions()
	{
		return $this->hasMany(UpPermission::class, 'updated_by_id');
	}

	public function up_roles()
	{
		return $this->hasMany(UpRole::class, 'updated_by_id');
	}

	public function up_users()
	{
		return $this->hasMany(UpUser::class, 'updated_by_id');
	}
}
