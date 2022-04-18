<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommentsCommentAuthorUserLink
 * 
 * @property int|null $comment_id
 * @property int|null $user_id
 * 
 * @property CommentsComment|null $comments_comment
 * @property UpUser|null $up_user
 *
 * @package App\Models
 */
class CommentsCommentAuthorUserLink extends Model
{
	protected $table = 'comments_comment_author_user_links';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'comment_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'comment_id',
		'user_id'
	];

	public function comments_comment()
	{
		return $this->belongsTo(CommentsComment::class, 'comment_id');
	}

	public function up_user()
	{
		return $this->belongsTo(UpUser::class, 'user_id');
	}
}
