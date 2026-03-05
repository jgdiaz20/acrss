<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('teacher_subjects', 'is_primary')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->dropColumn('is_primary');
            });
        }

        if (Schema::hasColumn('teacher_subjects', 'experience_years')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->dropColumn('experience_years');
            });
        }

        if (Schema::hasColumn('teacher_subjects', 'notes')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('teacher_subjects', 'is_primary')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->boolean('is_primary')->default(false)->after('subject_id');
            });
        }

        if (!Schema::hasColumn('teacher_subjects', 'experience_years')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->integer('experience_years')->nullable()->after('is_primary');
            });
        }

        if (!Schema::hasColumn('teacher_subjects', 'notes')) {
            Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('experience_years');
            });
        }
    }
};
