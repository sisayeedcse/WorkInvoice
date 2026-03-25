<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            // Link production orders to projects for workshop custom work
            $table->unsignedBigInteger('project_id')->nullable()->after('finished_item_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');

            // Add index for performance
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeignKey(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
