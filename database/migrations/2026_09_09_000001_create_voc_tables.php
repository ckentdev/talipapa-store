<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voc_utterances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 32);
            $table->text('original_text');
            $table->string('language', 32)->nullable();
            $table->string('intent', 64)->nullable();
            $table->unsignedInteger('result_count')->default(0);
            $table->timestamps();

            $table->index(['source', 'created_at']);
            $table->index('result_count');
        });

        Schema::create('voc_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voc_utterance_id')->constrained('voc_utterances')->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('value');
            $table->string('polarity', 16)->default('include');
            $table->timestamps();

            $table->index(['type', 'value']);
            $table->index('voc_utterance_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voc_attributes');
        Schema::dropIfExists('voc_utterances');
    }
};
