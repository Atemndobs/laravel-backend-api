<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesRelatedMorphsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_related_morphs', function (Blueprint $table) {
            $table->unsignedInteger('file_id')->nullable()->index('files_related_morphs_fk');
            $table->unsignedInteger('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->string('field')->nullable();
            $table->unsignedInteger('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_related_morphs');
    }
}
