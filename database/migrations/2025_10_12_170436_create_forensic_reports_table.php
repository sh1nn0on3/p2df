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
        Schema::create('forensic_reports', function (Blueprint $table) {
            $table->id();
            
            // Liên kết tới email và investigator
            $table->foreignId('email_id')->constrained('emails')->onDelete('cascade');
            $table->foreignId('investigator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('decryption_request_id')->nullable()->constrained('decryption_requests')->onDelete('set null');
            
            // Nội dung báo cáo
            $table->string('title');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->longText('findings'); // Phát hiện chính
            $table->longText('analysis'); // Phân tích chi tiết
            $table->longText('recommendations')->nullable(); // Khuyến nghị
            $table->json('related_logs')->nullable(); // IDs của logs liên quan
            
            // Metadata
            $table->enum('status', ['draft', 'completed', 'reviewed'])->default('draft');
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['email_id', 'investigator_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forensic_reports');
    }
};
