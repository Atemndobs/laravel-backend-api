<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentsCommentThreadOfLink
 * 
 * @property int|null $comment_id
 * @property int|null $inv_comment_id
 * 
 * @property CommentsComment|null $comments_comment
 *
 * @package App\Models
 */
class CommentsCommentThreadOfLink extends Model
{
	protected $table = 'comments_comment_thread_of_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'comment_id' => 'int',
		'inv_comment_id' => 'int'
	];

	protected $fillable = [
		'comment_id',
		'inv_comment_id'
	];

	public function comments_comment()
	{
		return $this->belongsTo(CommentsComment::class, 'inv_comment_id');
	}
}
