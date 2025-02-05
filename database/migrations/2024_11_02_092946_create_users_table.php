<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_auth_id')
                ->constrained('user_auth', 'id')
                ->cascadeOnDelete()->name('users_user_auth_id_fk')->index();

            $table->foreignId('user_role_id')
                    ->constrained('user_roles', 'id')
                    ->cascadeOnDelete()->name('users_user_role_id_fk')->index();

            $table->string('fullname');
            $table->string('phone')->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->default(null)->nullable();

            $table->boolean('is_active')->default(1)->index();
            $table->boolean('is_blocked')->default(0)->index();

            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletes()->index();
        });


        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestamp('created_at')->nullable();
        // });

        // Schema::create('sessions', function (Blueprint $table) {
        //     $table->string('id')->primary();
        //     $table->foreignId('user_id')->nullable()->index();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->longText('payload');
        //     $table->integer('last_activity')->index();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
