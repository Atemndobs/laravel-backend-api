<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Song
 * 
 * @property int $id
 * @property string|null $title
 * @property bool|null $analyzed
 * @property string|null $status
 * @property string|null $key
 * @property string|null $scale
 * @property float|null $bpm
 * @property float|null $energy
 * @property float|null $happy
 * @property float|null $sad
 * @property float|null $aggressiveness
 * @property float|null $danceability
 * @property float|null $relaxed
 * @property string|null $path
 * @property string|null $related_songs
 * @property string|null $extension
 * @property array|null $genre
 * @property string|null $author
 * @property string|null $comment
 * @property string|null $source
 * @property string|null $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property string|null $image
 * @property bool|null $played
 * 
 * @property AdminUser|null $admin_user
 *
 * @package App\Models\Base
 */
class Song extends Model
{
	protected $table = 'songs';

	protected $casts = [
		'analyzed' => 'bool',
		'bpm' => 'float',
		'energy' => 'float',
		'happy' => 'float',
		'sad' => 'float',
		'aggressiveness' => 'float',
		'danceability' => 'float',
		'relaxed' => 'float',
		'genre' => 'json',
		'created_by_id' => 'int',
		'updated_by_id' => 'int',
		'played' => 'bool'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}
}
