<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('files');
            $table->enum('type', ['a', 'v', 'd', 'i', 'o', 'f'])->nullable();
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('extension', 8)->nullable();
            $table->string('mimetype', 100)->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('filesize')->nullable();
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('description')->default(new Expression('(JSON_OBJECT())'));
            $table->json('alt_attribute')->default(new Expression('(JSON_OBJECT())'));
            $table->unsignedTinyInteger('focal_x')->nullable();
            $table->unsignedTinyInteger('focal_y')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('files');
    }
}
