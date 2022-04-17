<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('image_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('class')->nullable();
            $table->json('status');
            $table->timestamps();
        });
        Schema::create('menulinks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('menu_id')->unsigned();
            $table->integer('page_id')->unsigned()->nullable();
            $table->integer('section_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable()->default(null);
            $table->integer('image_id')->unsigned()->nullable();
            $table->integer('position')->unsigned()->default(0);
            $table->string('target', 10)->nullable();
            $table->string('class')->nullable();
            $table->json('status');
            $table->json('title');
            $table->json('url');
            $table->json('description');
            $table->timestamps();
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('menulinks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('menulinks');
        Schema::drop('menus');
    }
}
