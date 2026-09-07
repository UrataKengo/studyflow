<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cards', 'study_count')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->unsignedInteger('study_count')
                    ->default(0)
                    ->after('review_count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cards', 'study_count')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->dropColumn('study_count');
            });
        }
    }
};