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
        Schema::create('reservation_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['h-1', 'h-0']); // h-1 = 1 day before, h-0 = same day
            $table->string('message');
            $table->boolean('sent')->default(false);
            $table->boolean('is_read')->default(false); // Combined from separate migration
            $table->timestamp('scheduled_at'); // When the notification should be sent
            $table->timestamp('sent_at')->nullable(); // When it was actually sent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_reminders');
    }
};
