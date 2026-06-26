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
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('status')->default('Pending')->after('id');
        });

        // Set existing recipes to Approved to avoid hiding them all immediately
        \Illuminate\Support\Facades\DB::table('recipes')->update(['status' => 'Approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
