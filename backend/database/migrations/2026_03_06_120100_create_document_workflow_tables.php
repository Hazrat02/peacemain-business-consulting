<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('document_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('source_country', 100)->nullable();
            $table->string('destination_country', 100)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('passport_type', 50)->nullable();
            $table->boolean('is_exclusion')->default(false);
            $table->timestamps();
        });

        Schema::create('user_required_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->string('status', 20)->default('missing');
            $table->unsignedBigInteger('latest_submission_id')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'document_id']);
        });

        Schema::create('document_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_required_document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 150)->nullable();
            $table->string('review_status', 20)->default('pending');
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('document_review_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_required_document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_submission_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->string('action', 20);
            $table->text('note')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('document_review_logs');
        Schema::dropIfExists('user_required_documents');
        Schema::dropIfExists('document_submissions');
        Schema::dropIfExists('document_rules');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_sections');
    }
};
