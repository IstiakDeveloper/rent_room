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
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('footer_section_four_id');
            $table->string('icon_class')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();

            $table->foreign('footer_section_four_id')->references('id')->on('footer_section_fours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
