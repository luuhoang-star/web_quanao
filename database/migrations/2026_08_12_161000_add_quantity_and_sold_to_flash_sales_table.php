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
        Schema::table('flash_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('flash_sales', 'quantity')) {
                $table->integer('quantity')->default(10)->after('end_time');
            }
            if (! Schema::hasColumn('flash_sales', 'sold_quantity')) {
                $table->integer('sold_quantity')->default(0)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'sold_quantity']);
        });
    }
};
