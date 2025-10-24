<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProgramForeignKeysOnDelete extends Migration
{
    public function up()
    {
        Schema::table('grade_levels', function (Blueprint $table) {
            // Drop and recreate FK with ON DELETE CASCADE so grade levels are removed with their program
            $table->dropForeign('program_fk_1001500');
            $table->foreign('program_id', 'program_fk_1001500')
                ->references('id')->on('academic_programs')
                ->onDelete('cascade');
        });

        Schema::table('school_classes', function (Blueprint $table) {
            // Drop and recreate FKs so classes survive program/grade deletion
            $table->dropForeign('program_fk_1001501');
            $table->foreign('program_id', 'program_fk_1001501')
                ->references('id')->on('academic_programs')
                ->onDelete('set null');

            $table->dropForeign('grade_level_fk_1001502');
            $table->foreign('grade_level_id', 'grade_level_fk_1001502')
                ->references('id')->on('grade_levels')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('grade_levels', function (Blueprint $table) {
            $table->dropForeign('program_fk_1001500');
            $table->foreign('program_id', 'program_fk_1001500')
                ->references('id')->on('academic_programs');
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign('program_fk_1001501');
            $table->foreign('program_id', 'program_fk_1001501')
                ->references('id')->on('academic_programs');

            $table->dropForeign('grade_level_fk_1001502');
            $table->foreign('grade_level_id', 'grade_level_fk_1001502')
                ->references('id')->on('grade_levels');
        });
    }
}


