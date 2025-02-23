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
        Schema::table('favoritos', function (Blueprint $table) {
            $table->string('cat_url')->after('cat_api_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favoritos', function (Blueprint $table) {
            $table->dropColumn('cat_url');
        });
    }
};