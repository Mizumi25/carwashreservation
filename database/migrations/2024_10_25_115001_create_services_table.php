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
        Schema::create('services', function (Blueprint $table) {
            $table->id(); 
            $table->string('service_name'); 
            $table->string('icon')->nullable(); 
            $table->text('description')->nullable(); 
            $table->float('price', 8, 2); 
            $table->integer('duration'); 
            $table->boolean('is_active')->default(true); 
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('popularity')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services'); 
    }
};
