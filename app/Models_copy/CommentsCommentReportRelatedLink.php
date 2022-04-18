<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentsCommentReportRelatedLink
 * 
 * @property int|null $comment_report_id
 * @property int|null $comment_id
 * 
 * @property CommentsCommentReport|null $comments_comment_report
 * @property CommentsComment|null $comments_comment
 *
 * @package App\Models
 */
class CommentsCommentReportRelatedLink extends Model
{
	protected $table = 'comments_comment_report_related_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'comment_report_id' => 'int',
		'comment_id' => 'int'
	];

	protected $fillable = [
		'comment_report_id',
		'comment_id'
	];

	public function comments_comment_report()
	{
		return $this->belongsTo(CommentsCommentReport::class, 'comment_report_id');
	}

	public function comments_comment()
	{
		return $this->belongsTo(CommentsComment::class, 'comment_id');
	}
}
