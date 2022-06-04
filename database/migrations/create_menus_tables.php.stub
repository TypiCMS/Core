<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
            $table->id();
            $table->foreignId('image_id')->nullable()->constrained('files')->nullOnDelete();
            $table->string('name');
            $table->string('class')->nullable();
            $table->json('status')->default(new Expression('(JSON_OBJECT())'));
            $table->timestamps();
        });
        Schema::create('menulinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('page_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('page_sections')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menulinks')->onDelete('cascade');
            $table->foreignId('image_id')->nullable()->constrained('files')->nullOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->string('target', 10)->nullable();
            $table->string('class')->nullable();
            $table->json('status')->default(new Expression('(JSON_OBJECT())'));
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('url')->default(new Expression('(JSON_OBJECT())'));
            $table->json('description')->default(new Expression('(JSON_OBJECT())'));
            $table->timestamps();
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
