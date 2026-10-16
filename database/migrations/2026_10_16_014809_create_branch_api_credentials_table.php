<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branch_api_credentials')) {
            return;
        }

        Schema::create('branch_api_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 50)->unique();
            $table->text('api_key_encrypted');
            $table->timestamp('last_validated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_api_credentials');
    }
};
