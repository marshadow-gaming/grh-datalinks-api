<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->decimal('salaire', 12, 2)->nullable()->after('adresse');
            $table->integer('jours_conge_annuels')->nullable()->default(18)->after('salaire');
            $table->text('notes')->nullable()->after('jours_conge_annuels');
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropColumn(['salaire', 'jours_conge_annuels', 'notes']);
        });
    }
};