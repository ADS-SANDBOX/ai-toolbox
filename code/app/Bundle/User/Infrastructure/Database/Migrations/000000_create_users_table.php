<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $blueprint): void {
            $blueprint->string(column: 'id', length: 36)->primary();
            $blueprint->string(column: 'name');
            $blueprint->string(column: 'email')->unique();
            $blueprint->string(column: 'password');
            $blueprint->text(column: 'token')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
