<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->Integer('numero')->nullable();
            $table->Integer('numero_adulto_mayor')->nullable();
            $table->boolean('es_adulto_mayor')->default(false);
            $table->string('estado');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
