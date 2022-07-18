<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrapiWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strapi_webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->longText('url')->nullable();
            $table->json('headers')->nullable();
            $table->json('events')->nullable();
            $table->boolean('enabled')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strapi_webhooks');
    }
}
