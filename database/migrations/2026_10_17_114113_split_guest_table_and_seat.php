<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('table_name')->nullable()->after('name');
            $table->string('seat_position', 50)->nullable()->after('table_name');
        });

        DB::table('guests')->whereNull('table_name')->update([
            'table_name' => DB::raw('seat'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['table_name', 'seat_position']);
        });
    }
};
