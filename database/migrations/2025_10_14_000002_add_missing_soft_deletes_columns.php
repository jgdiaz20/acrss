<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingSoftDeletesColumns extends Migration
{
    public function up()
    {
        // Ensure academic_programs has deleted_at
        if (!Schema::hasColumn('academic_programs', 'deleted_at')) {
            Schema::table('academic_programs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Ensure grade_levels has deleted_at
        if (!Schema::hasColumn('grade_levels', 'deleted_at')) {
            Schema::table('grade_levels', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Ensure school_classes has deleted_at
        if (!Schema::hasColumn('school_classes', 'deleted_at')) {
            Schema::table('school_classes', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        // Non-destructive: we won't drop columns on rollback to avoid data loss
    }
}


