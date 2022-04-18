<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StrapiDatabaseSchema
 * 
 * @property int $id
 * @property array|null $schema
 * @property Carbon|null $time
 * @property string|null $hash
 *
 * @package App\Models
 */
class StrapiDatabaseSchema extends Model
{
	protected $table = 'strapi_database_schema';
	public $timestamps = false;

	protected $casts = [
		'schema' => 'json'
	];

	protected $dates = [
		'time'
	];

	protected $fillable = [
		'schema',
		'time',
		'hash'
	];
}
