<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['oficial', 'blue', 'mep']);
            $table->decimal('compra', 10, 2);
            $table->decimal('venta', 10, 2);
            $table->date('fecha');
            $table->timestamps();
            
            // Índices para consultas rápidas
            $table->index(['tipo', 'fecha']);
            $table->unique(['tipo', 'fecha']); // Una cotización por tipo por día
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};