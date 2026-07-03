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
        Schema::create('offres_emploi', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->foreignId('departement_id')->constrained('departements')->onDelete('cascade');
            $table->text('description');
            $table->enum('type_contrat', ['CDI', 'CDD', 'Stage', 'Consultant']);
            $table->enum('statut', ['ouverte', 'fermee'])->default('ouverte');
            $table->foreignId('publiee_par')->constrained('users')->onDelete('cascade');
            $table->date('date_publication');
            $table->date('date_limite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres_emploi');
    }
};
