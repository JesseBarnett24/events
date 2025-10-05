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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');                      // Event name/title
            $table->text('description')->nullable();      // Optional details
            $table->dateTime('starts_at');                // When the event begins
            $table->string('location')->nullable();       // Optional location
            $table->integer('capacity')->nullable();      // Max attendees
            $table->foreignId('organiser_id')             // Link to organiser (User)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
