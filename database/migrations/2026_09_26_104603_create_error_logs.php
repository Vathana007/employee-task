<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('error_type');
            $table->string('message');
            $table->text('stack_trace')->nullable();
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method')->nullable();
            $table->string('endpoint')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('ip_address');
            $table->index('user_id');
            $table->index('error_type');
            $table->index('created_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
