<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Users table - improve role-based queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_admin', 'users_is_admin_index');
            $table->index('is_teacher', 'users_is_teacher_index');
            $table->index('class_id', 'users_class_id_index');
        });

        // School classes table - improve filtering
        Schema::table('school_classes', function (Blueprint $table) {
            $table->index('program_id', 'school_classes_program_id_index');
            $table->index('grade_level_id', 'school_classes_grade_level_id_index');
            $table->index('is_active', 'school_classes_is_active_index');
        });

        // Subjects table - improve filtering
        Schema::table('subjects', function (Blueprint $table) {
            $table->index('is_active', 'subjects_is_active_index');
            $table->index('requires_lab', 'subjects_requires_lab_index');
            $table->index('requires_equipment', 'subjects_requires_equipment_index');
        });

        // Rooms table - improve filtering
        Schema::table('rooms', function (Blueprint $table) {
            $table->index('is_lab', 'rooms_is_lab_index');
            $table->index('has_equipment', 'rooms_has_equipment_index');
        });

        // Teacher subjects pivot table - improve lookup performance
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->index('is_active', 'teacher_subjects_is_active_index');
            $table->index(['teacher_id', 'subject_id'], 'teacher_subjects_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_is_admin_index');
            $table->dropIndex('users_is_teacher_index');
            $table->dropIndex('users_class_id_index');
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropIndex('school_classes_program_id_index');
            $table->dropIndex('school_classes_grade_level_id_index');
            $table->dropIndex('school_classes_is_active_index');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('subjects_is_active_index');
            $table->dropIndex('subjects_requires_lab_index');
            $table->dropIndex('subjects_requires_equipment_index');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_is_lab_index');
            $table->dropIndex('rooms_has_equipment_index');
        });

        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropIndex('teacher_subjects_is_active_index');
            $table->dropIndex('teacher_subjects_lookup_index');
        });
    }
}
