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
        if (! Schema::hasColumn('sizes', 'code')) {
            Schema::table('sizes', function (Blueprint $table) {
                $table->string('code', 50)->nullable()->after('name')->unique();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sizes', 'code')) {
            Schema::table('sizes', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};
