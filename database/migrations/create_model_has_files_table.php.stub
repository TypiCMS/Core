<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelHasFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('model_has_files', function (Blueprint $table) {
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->morphs('model', 'file');
            $table->unsignedInteger('position');
            $table->primary(['file_id', 'model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('model_has_files');
    }
}
