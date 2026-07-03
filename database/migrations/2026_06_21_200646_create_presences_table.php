<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->date('date');
            $table->time('heure_arrivee')->nullable();
            $table->time('heure_depart')->nullable();
            $table->foreignId('scanne_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['employe_id', 'date']); // une seule ligne par employé par jour
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};