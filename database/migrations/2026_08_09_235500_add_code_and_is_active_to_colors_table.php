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
        Schema::table('colors', function (Blueprint $table) {
            if (! Schema::hasColumn('colors', 'code')) {
                $table->string('code', 50)->nullable()->after('hex_code');
            }
            if (! Schema::hasColumn('colors', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colors', function (Blueprint $table) {
            if (Schema::hasColumn('colors', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('colors', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
