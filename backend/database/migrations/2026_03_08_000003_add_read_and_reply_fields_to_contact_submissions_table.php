<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->boolean('is_read')->default(false)->after('status');
            $table->timestamp('read_at')->nullable()->after('is_read');
            $table->timestamp('replied_at')->nullable()->after('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table): void {
            $table->dropColumn(['is_read', 'read_at', 'replied_at']);
        });
    }
};

