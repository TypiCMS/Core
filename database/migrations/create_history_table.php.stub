<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('historable_id');
            $table->string('historable_type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('locale')->nullable();
            $table->string('historable_table');
            $table->string('action');
            $table->json('old')->default(new Expression('(JSON_OBJECT())'));
            $table->json('new')->default(new Expression('(JSON_OBJECT())'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('history');
    }
}
