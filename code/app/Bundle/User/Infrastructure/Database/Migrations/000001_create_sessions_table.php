<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $blueprint): void {
            $blueprint->string(column: 'id')->primary();
            $blueprint->string(column: 'user_id', length: 36)->nullable()->index(); // Cambiado a string(36) para UUID
            $blueprint->string(column: 'ip_address', length: 45)->nullable();
            $blueprint->text(column: 'user_agent')->nullable();
            $blueprint->longText(column: 'payload');
            $blueprint->integer(column: 'last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
