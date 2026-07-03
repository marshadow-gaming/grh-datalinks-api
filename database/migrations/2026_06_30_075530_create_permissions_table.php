<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('module', ['absences', 'scanner', 'historique_presence', 'travaux_stagiaire', 'recrutement']);
            $table->foreignId('accorde_par')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'module']); // évite les doublons
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};