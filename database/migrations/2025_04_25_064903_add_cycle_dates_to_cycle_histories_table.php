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
       Schema::table('cyle_histories', function (Blueprint $table) {
           // these will be NULL for all existing rows, so no data lost!
           $table->date('cycle_start_date')
                 ->nullable()
                 ->after('symptoms');
           $table->date('period_start_date')
                 ->nullable()
                 ->after('cycle_start_date');
           $table->date('period_end_date')
                 ->nullable()
                 ->after('period_start_date');
           $table->date('cycle_end_date')
                 ->nullable()
                 ->after('period_end_date');
       });
   }

   /**
    * Reverse the migrations.
    */
   public function down(): void
   {
       Schema::table('cyle_histories', function (Blueprint $table) {
           $table->dropColumn([
               'cycle_start_date',
               'period_start_date',
               'period_end_date',
               'cycle_end_date',
           ]);
       });
   }
};
