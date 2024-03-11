<?php

use App\Models\Thread;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Thread::class)->constrained()->cascadeOnDelete();
            $table->string('type', 16)->nullable(false)->default('email');
            $table->json('data')->nullable();
            $table->foreignId('from_person_id')->constrained('people');
            $table->foreignId('to_person_id')->constrained('people');
            $table->foreignId('parent_id')->nullable()->constrained('messages')->cascadeOnDelete();
            $table->datetime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
