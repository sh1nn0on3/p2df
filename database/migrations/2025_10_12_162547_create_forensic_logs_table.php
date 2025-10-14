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
        Schema::create('forensic_logs', function (Blueprint $table) {
            $table->id();
            
            // User thực hiện hành động
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Role của user (admin/investigator)
            $table->enum('role', ['admin', 'investigator']);
            
            // Hành động: upload, request, approve, reject, decrypt, view
            $table->string('action', 50);
            
            // ID đối tượng liên quan (email_id, request_id, etc.)
            $table->string('target_id')->nullable();
            
            // IP address của user
            $table->string('ip_address', 45)->nullable();
            
            // Chi tiết bổ sung (JSON)
            $table->json('details')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // Index cho tìm kiếm log
            $table->index(['user_id', 'role', 'action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forensic_logs');
    }
};
