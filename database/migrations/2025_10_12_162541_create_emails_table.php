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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            
            // Metadata email
            $table->string('from');
            $table->string('to');
            $table->string('subject');
            
            // Nội dung email đã mã hóa bằng AES
            $table->longText('body_encrypted');
            
            // AES key đã được mã hóa bằng Public Key của Admin
            $table->text('aes_key_encrypted_admin');
            
            // Hash để verify tính toàn vẹn
            $table->string('hash')->nullable();
            
            $table->timestamps();
            
            // Index cho tìm kiếm
            $table->index(['from', 'to', 'subject']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
