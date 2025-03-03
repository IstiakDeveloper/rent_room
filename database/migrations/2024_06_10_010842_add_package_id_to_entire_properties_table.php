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
        Schema::table('entire_properties', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable()->after('id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entire_properties', function (Blueprint $table) {
            $table->dropColumn('package_id');
        });
    }
};
