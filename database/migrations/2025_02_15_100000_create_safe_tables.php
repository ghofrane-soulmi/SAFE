<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET NAMES utf8');

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('full_name');
            $table->string('company')->nullable();
            $table->string('sector', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('role', 50)->default('participant');
            $table->string('profile_picture')->nullable();
            $table->string('linkedin_id')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->index('email');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('title')->unique();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('role_id');
            $table->timestamps();

            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('location')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('event_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('room', 100)->nullable();
            $table->integer('capacity')->nullable();
            $table->string('session_type', 50)->nullable();
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('session_id');
            $table->string('status', 50)->default('registered');
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['user_id', 'session_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('sender_id');
            $table->uuid('receiver_id');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['sender_id', 'receiver_id']);
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('session_id');
            $table->uuid('user_id')->nullable();
            $table->text('content');
            $table->integer('votes')->default(0);
            $table->string('status', 50)->default('pending');
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('question_votes', function (Blueprint $table) {
            $table->uuid('question_id');
            $table->uuid('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['question_id', 'user_id']);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 50);
            $table->string('url');
            $table->uuid('session_id')->nullable();
            $table->uuid('uploader_id')->nullable();
            $table->timestamps();
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
            $table->foreign('uploader_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('user_id');
            $table->string('title');
            $table->text('content');
            $table->string('type', 50);
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });

        Schema::create('contact_requests', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('sender_id');
            $table->uuid('receiver_id');
            $table->string('status', 50)->default('pending');
            $table->timestamps();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('question_votes');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('events');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
