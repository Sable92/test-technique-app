<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute une contrainte unique pour empêcher qu'un administrateur
     * commente plusieurs fois le même profil
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unique(['administrator_id', 'profile_id'], 'unique_admin_profile_comment');
        });
    }

    /**
     * Supprime la contrainte unique
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropUnique('unique_admin_profile_comment');
        });
    }
};
