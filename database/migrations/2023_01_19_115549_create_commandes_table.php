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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_client_id');
            $table->unsignedBigInteger('user_livreur_id')->nullable();
            $table->unsignedBigInteger('ville_id');
            $table->unsignedBigInteger('statut_cmd_id');
            $table->unsignedBigInteger('statut_livraison_id');
            $table->string('lieu_livraison');
            $table->string('montant_total');
            $table->string('signature_client')->nullable();
            $table->boolean('published')->default(1);
            $table->string('ref')->unique();
            $table->string('alias');
            $table->foreign('statut_livraison_id')->references('id')->on('statut_livraisons');
            $table->foreign('statut_cmd_id')->references('id')->on('statut_cmdes');
            $table->foreign('ville_id')->references('id')->on('villes');
            $table->foreign('user_livreur_id')->references('id')->on('users');
            $table->foreign('user_client_id')->references('id')->on('users');
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
        Schema::dropIfExists('commandes');
    }
};
