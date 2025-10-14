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
        Schema::table('users', function (Blueprint $table) {
            // Thêm role enum cho phân quyền admin/investigator
            $table->enum('role', ['admin', 'investigator'])->default('investigator')->after('password');
            
            // Đường dẫn lưu public key và private key của user
            $table->string('public_key_path')->nullable()->after('role');
            $table->string('private_key_path')->nullable()->after('public_key_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'public_key_path', 'private_key_path']);
        });
    }
};
