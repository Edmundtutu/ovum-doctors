<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('respiratory_rate');
            $table->decimal('hemoglobin', 4, 1);
            $table->decimal('hcg_initial', 8, 2);
            $table->decimal('hcg_followup', 8, 2)->nullable();
            $table->decimal('fsh', 6, 2);
            $table->decimal('lh', 6, 2);
            $table->decimal('fsh_lh_ratio', 5, 2);
            $table->decimal('waist_hip_ratio', 4, 2);
            $table->decimal('tsh', 6, 3);
            $table->decimal('amh', 4, 2);
            $table->decimal('prolactin', 5, 2);
            $table->decimal('vitamin_d3', 5, 2);
            $table->decimal('progesterone', 5, 2);
            $table->decimal('rbs', 5, 2);
            $table->unsignedSmallInteger('bp_systolic');
            $table->unsignedSmallInteger('bp_diastolic');
            $table->unsignedTinyInteger('total_follicles');
            $table->decimal('avg_fallopian_size', 5, 1);
            $table->decimal('endometrium', 4, 1);
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labs');
    }
};
