<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('image_id')->unsigned()->nullable();
            $table->integer('position')->unsigned()->default(0);
            $table->integer('parent_id')->unsigned()->nullable()->default(null);
            $table->boolean('private')->default(0);
            $table->boolean('is_home')->default(0);
            $table->boolean('redirect')->default(0);
            $table->json('title');
            $table->json('slug');
            $table->json('uri');
            $table->json('body');
            $table->json('status');
            $table->json('meta_keywords');
            $table->json('meta_description');
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->string('module')->nullable();
            $table->string('template')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('pages')->onDelete('cascade');
        });
        Schema::create('page_sections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->integer('image_id')->unsigned()->nullable();
            $table->integer('position')->unsigned()->default(0);
            $table->json('status');
            $table->json('title');
            $table->json('slug');
            $table->json('body');
            $table->timestamps();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('page_sections');
        Schema::drop('pages');
    }
}
