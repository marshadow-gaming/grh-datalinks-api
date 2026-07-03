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
        Schema::create('demandes_absence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->enum('type', ['conge', 'permission']);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->text('motif');
            $table->enum('statut', ['en_attente', 'approuvee', 'rejetee'])->default('en_attente');
            $table->foreignId('traite_par')->nullable()->constrained('users')->onDelete('set null');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_absence');
    }
};
