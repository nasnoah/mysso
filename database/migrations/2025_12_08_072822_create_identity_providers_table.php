<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('identity_providers', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->index()
                ->constrained((new User)->getTable())
                ->cascadeOnDelete();

            $table->string('provider_id');
            $table->string('provider_name');
            $table->string('provider_email')->nullable();
            $table->string('provider_avatar')->nullable();
            $table->text('provider_token')->nullable();
            $table->text('provider_refresh_token')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_providers');
    }
};
