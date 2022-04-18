<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentsCommentReport
 * 
 * @property int $id
 * @property string|null $content
 * @property string|null $reason
 * @property bool|null $resolved
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property CommentsCommentReportRelatedLink $comments_comment_report_related_link
 *
 * @package App\Models
 */
class CommentsCommentReport extends Model
{
	protected $table = 'comments_comment-report';

	protected $casts = [
		'resolved' => 'bool',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'content',
		'reason',
		'resolved',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function comments_comment_report_related_link()
	{
		return $this->hasOne(CommentsCommentReportRelatedLink::class, 'comment_report_id');
	}
}
