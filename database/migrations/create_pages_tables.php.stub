<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
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
            $table->id();
            $table->foreignId('image_id')->nullable()->constrained('files')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('pages');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('private')->default(0);
            $table->boolean('is_home')->default(0);
            $table->boolean('redirect')->default(0);
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('slug')->default(new Expression('(JSON_OBJECT())'));
            $table->json('uri')->default(new Expression('(JSON_OBJECT())'));
            $table->json('body')->default(new Expression('(JSON_OBJECT())'));
            $table->json('status')->default(new Expression('(JSON_OBJECT())'));
            $table->json('meta_keywords')->default(new Expression('(JSON_OBJECT())'));
            $table->json('meta_description')->default(new Expression('(JSON_OBJECT())'));
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->string('module')->nullable();
            $table->string('template')->nullable();
            $table->timestamps();
        });
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->foreignId('image_id')->nullable()->constrained('files')->nullOnDelete();
            $table->unsignedInteger('position');
            $table->string('template');
            $table->json('status')->default(new Expression('(JSON_OBJECT())'));
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('slug')->default(new Expression('(JSON_OBJECT())'));
            $table->json('body')->default(new Expression('(JSON_OBJECT())'));
            $table->timestamps();
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
