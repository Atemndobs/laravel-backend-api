<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StrapiWebhook
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $url
 * @property array|null $headers
 * @property array|null $events
 * @property bool|null $enabled
 *
 * @package App\Models
 */
class StrapiWebhook extends Model
{
	protected $table = 'strapi_webhooks';
	public $timestamps = false;

	protected $casts = [
		'headers' => 'json',
		'events' => 'json',
		'enabled' => 'bool'
	];

	protected $fillable = [
		'name',
		'url',
		'headers',
		'events',
		'enabled'
	];
}
