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
        Schema::create('decryption_requests', function (Blueprint $table) {
            $table->id();
            
            // Liên kết tới email cần giải mã
            $table->foreignId('email_id')->constrained('emails')->onDelete('cascade');
            
            // Investigator gửi yêu cầu
            $table->foreignId('investigator_id')->constrained('users')->onDelete('cascade');
            
            // Trạng thái: pending, approved, rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Lý do yêu cầu giải mã
            $table->text('reason')->nullable();
            
            // AES key đã được Admin mã hóa lại bằng Public Key của Investigator
            // (chỉ có khi status = approved)
            $table->text('aes_key_encrypted_inv')->nullable();
            
            // Thời gian phê duyệt
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['email_id', 'investigator_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decryption_requests');
    }
};
