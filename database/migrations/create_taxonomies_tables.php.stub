<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomiesTables extends Migration
{
    public function up()
    {
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position');
            $table->string('name');
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('slug')->default(new Expression('(JSON_OBJECT())'));
            $table->string('validation_rule')->nullable();
            $table->json('result_string')->default(new Expression('(JSON_OBJECT())'));
            $table->json('modules')->default(new Expression('(JSON_ARRAY())'));
            $table->timestamps();
        });
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position');
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->json('title')->default(new Expression('(JSON_OBJECT())'));
            $table->json('slug')->default(new Expression('(JSON_OBJECT())'));
            $table->timestamps();
        });
        Schema::create('model_has_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->foreignId('model_id');
            $table->string('model_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('model_has_terms');
        Schema::drop('terms');
        Schema::drop('taxonomies');
    }
}
