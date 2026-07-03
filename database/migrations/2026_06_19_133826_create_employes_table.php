<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('departement_id')->constrained('departements')->onDelete('cascade');
            $table->string('matricule')->unique();
            $table->string('poste')->nullable();
            $table->enum('type_contrat', ['CDI', 'CDD', 'Stage', 'Consultant'])->nullable();
            $table->date('date_embauche')->nullable();
            $table->date('date_fin_contrat')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('adresse')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
