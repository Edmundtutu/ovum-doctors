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
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_id')->unique()->index(); // USSD session identifier
            $table->string('phone_number');                  // User phone number
            $table->string('service_code')->nullable();      // USSD service code
            $table->text('token')->nullable();               // Auth token for API calls
            $table->json('data')->nullable();                // Any additional session data
            $table->boolean('authenticated')->default(false); // Authentication status
            $table->timestamps();                            // Created/updated timestamps
            $table->timestamp('expires_at')->nullable();     // Session expiry
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
