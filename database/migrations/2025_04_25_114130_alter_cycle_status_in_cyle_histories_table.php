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
        // Remove the old bo ==olean column
        Schema::table('cyle_histories', function (Blueprint $table) {
            $table->dropColumn('cycle_status');
        });

        // Add new enum column
        Schema::table('cyle_histories', function (Blueprint $table) {
            $table->enum('cycle_status', ['new','in_progress','completed'])
                  ->default('new')
                  ->after('symptoms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to boolean
        Schema::table('cyle_histories', function (Blueprint $table) {
            $table->dropColumn('cycle_status');
        });

        Schema::table('cyle_histories', function (Blueprint $table) {
            $table->boolean('cycle_status')
                  ->default(false)
                  ->after('symptoms');
        });
    }
};
