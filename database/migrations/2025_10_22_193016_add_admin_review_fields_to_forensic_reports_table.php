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
        Schema::table('forensic_reports', function (Blueprint $table) {
            $table->timestamp('admin_reviewed_at')->nullable();
            $table->unsignedBigInteger('admin_reviewed_by')->nullable();
            $table->text('admin_notes')->nullable();
            $table->enum('admin_action', ['approved', 'rejected', 'needs_revision'])->nullable();
            
            $table->foreign('admin_reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forensic_reports', function (Blueprint $table) {
            $table->dropForeign(['admin_reviewed_by']);
            $table->dropColumn([
                'admin_reviewed_at',
                'admin_reviewed_by', 
                'admin_notes',
                'admin_action'
            ]);
        });
    }
};
