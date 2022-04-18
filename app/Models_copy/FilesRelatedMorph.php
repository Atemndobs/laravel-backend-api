<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FilesRelatedMorph
 * 
 * @property int|null $file_id
 * @property int|null $related_id
 * @property string|null $related_type
 * @property string|null $field
 * @property int|null $order
 * 
 * @property File|null $file
 *
 * @package App\Models
 */
class FilesRelatedMorph extends Model
{
	protected $table = 'files_related_morphs';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'file_id' => 'int',
		'related_id' => 'int',
		'order' => 'int'
	];

	protected $fillable = [
		'file_id',
		'related_id',
		'related_type',
		'field',
		'order'
	];

	public function file()
	{
		return $this->belongsTo(File::class);
	}
}
