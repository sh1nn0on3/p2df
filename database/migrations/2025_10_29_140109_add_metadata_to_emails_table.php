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
        Schema::table('emails', function (Blueprint $table) {
            // Email metadata cho điều tra số
            $table->datetime('date_sent')->nullable()->after('subject');
            $table->datetime('date_received')->nullable()->after('date_sent');
            
            // CC và BCC
            $table->text('cc')->nullable()->after('to');
            $table->text('bcc')->nullable()->after('cc');
            $table->string('reply_to')->nullable()->after('bcc');
            
            // Message identification
            $table->string('message_id')->nullable()->after('reply_to');
            
            // Headers và thông tin khác
            $table->text('headers')->nullable()->comment('Raw email headers as JSON')->after('message_id');
            $table->string('sender_ip')->nullable()->after('headers');
            $table->text('attachments_info')->nullable()->comment('Attachment metadata as JSON')->after('sender_ip');
            $table->string('mailer')->nullable()->comment('Email client/software')->after('attachments_info');
            
            // Indexes cho tìm kiếm
            $table->index('date_sent');
            $table->index('date_received');
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn([
                'date_sent',
                'date_received',
                'cc',
                'bcc',
                'reply_to',
                'message_id',
                'headers',
                'sender_ip',
                'attachments_info',
                'mailer'
            ]);
        });
    }
};
