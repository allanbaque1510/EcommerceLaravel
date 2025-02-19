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
        Schema::create('carrito', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_session')->unique(); // Identificador de sesión
            $table->timestamps();
        });

        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_carrito'); // Relación con el carrito
            $table->unsignedBigInteger('id_producto'); // Producto agregado
            $table->integer('cantidad')->default(1);
            $table->foreign('id_carrito')->references('id')->on('carrito')->onDelete('cascade'); // Para enlazarlo cuando inicie sesión
            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade'); // Para enlazarlo cuando inicie sesión
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrito');
        Schema::dropIfExists('carrito_items');
    }
};
