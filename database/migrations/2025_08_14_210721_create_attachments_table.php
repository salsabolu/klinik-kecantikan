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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('foto_sebelum, foto_sesudah, hasil_lab, lainnya'); 
            $table->string('filename'); // nama file asli
            $table->string('path'); // path file di storage
            $table->string('mime_type'); // image/jpeg, application/pdf, etc
            $table->bigInteger('size'); // ukuran file dalam bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
