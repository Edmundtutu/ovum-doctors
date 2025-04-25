<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cyle_histories', function (Blueprint $table) {
                // add a NOT NULL boolean with default=false
                    $table->boolean('cycle_status')
                    ->default(false)
                    ->after('cycle_end_date');
        });

        // just to be 100% sure, explicitly set anything (though default handles it)
        DB::table('cyle_histories')->whereNull('cycle_status')->update([
            'cycle_status' => false,
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cyle_histories', function (Blueprint $table) {
            $table->dropColumn('cycle_status');
        });
    }
};
