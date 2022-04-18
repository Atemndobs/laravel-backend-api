<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $alternative_text
 * @property string|null $caption
 * @property int|null $width
 * @property int|null $height
 * @property array|null $formats
 * @property string|null $hash
 * @property string|null $ext
 * @property string|null $mime
 * @property float|null $size
 * @property string|null $url
 * @property string|null $preview_url
 * @property string|null $provider
 * @property array|null $provider_metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property FilesRelatedMorph $files_related_morph
 *
 * @package App\Models
 */
class File extends Model
{
	protected $table = 'files';

	protected $casts = [
		'width' => 'int',
		'height' => 'int',
		'formats' => 'json',
		'size' => 'float',
		'provider_metadata' => 'json',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	protected $fillable = [
		'name',
		'alternative_text',
		'caption',
		'width',
		'height',
		'formats',
		'hash',
		'ext',
		'mime',
		'size',
		'url',
		'preview_url',
		'provider',
		'provider_metadata',
		'created_by_id',
		'updated_by_id'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}

	public function files_related_morph()
	{
		return $this->hasOne(FilesRelatedMorph::class);
	}
}
