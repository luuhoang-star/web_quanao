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
        Schema::table('sizes', function (Blueprint $table) {
            if (! Schema::hasColumn('sizes', 'size_type')) {
                $table->string('size_type', 30)->default('OTHER')->after('code');
            }
            if (! Schema::hasColumn('sizes', 'specifications')) {
                $table->json('specifications')->nullable()->after('size_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizes', function (Blueprint $table) {
            if (Schema::hasColumn('sizes', 'specifications')) {
                $table->dropColumn('specifications');
            }
            if (Schema::hasColumn('sizes', 'size_type')) {
                $table->dropColumn('size_type');
            }
        });
    }
};
