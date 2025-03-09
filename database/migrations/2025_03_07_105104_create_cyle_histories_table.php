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
        /**
         * This table has been created to store only data from the history
         * of the app users/patients.
         * only the Mobile app will be storing and reading from it
         * the rest of acess ie from the doctor side would make reads with access
         */
        Schema::create('cyle_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('month');
            $table->integer('cycle_length');
            $table->integer('period_length');
            $table->json('symptoms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cyle_histories');
    }
};
