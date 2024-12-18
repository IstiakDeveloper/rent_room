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
        Schema::table('agreement_details', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->nullable()->after('amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreement_details', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->nullable()->after('amount');

        });
    }
};
