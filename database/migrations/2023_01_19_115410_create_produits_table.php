<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('categorie_id');
           // $table->unsignedBigInteger('sous_categorie_id')->nullable();
            $table->unsignedBigInteger('marque_id')->nullable();

            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('published')->default(1);
            $table->string('ref')->unique();
            $table->string('alias');
            $table->string('max_price');
            $table->string('min_price');
            $table->foreign('marque_id')->references('id')->on('marques');
            $table->foreign('categorie_id')->references('id')->on('categories');
            $table->foreign('user_id')->references('id')->on('users');     

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produits');
    }
};
