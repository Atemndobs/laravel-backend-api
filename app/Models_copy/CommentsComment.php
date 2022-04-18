<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentsComment
 * 
 * @property int $id
 * @property string|null $content
 * @property bool|null $blocked
 * @property bool|null $blocked_thread
 * @property string|null $block_reason
 * @property string|null $author_id
 * @property string|null $author_name
 * @property string|null $author_email
 * @property string|null $author_avatar
 * @property bool|null $removed
 * @property string|null $approval_status
 * @property string|null $related
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property CommentsCommentAuthorUserLink $comments_comment_author_user_link
 * @property CommentsCommentReportRelatedLink $comments_comment_report_related_link
 * @property CommentsCommentThreadOfLink $comments_comment_thread_of_link
 *
 * @package App\Models
 */
class CommentsComment extends Model
{
	protected $table = 'comments_comment';

	protected $casts = [
		'blocked' => 'bool',
		'blocked_thread' => 'bool',
		'removed' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'content',
		'blocked',
		'blocked_thread',
		'block_reason',
		'author_id',
		'author_name',
		'author_email',
		'author_avatar',
		'removed',
		'approval_status',
		'related',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function comments_comment_author_user_link()
	{
		return $this->hasOne(CommentsCommentAuthorUserLink::class, 'comment_id');
	}

	public function comments_comment_report_related_link()
	{
		return $this->hasOne(CommentsCommentReportRelatedLink::class, 'comment_id');
	}

	public function comments_comment_thread_of_link()
	{
		return $this->hasOne(CommentsCommentThreadOfLink::class, 'inv_comment_id');
	}
}
