<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgramFieldsToSchoolClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->unsignedInteger('program_id')->nullable();
            $table->unsignedInteger('grade_level_id')->nullable();
            $table->string('section')->nullable(); // "A", "B", "C", etc.
            $table->integer('max_students')->default(30);
            $table->boolean('is_active')->default(true);
            
            $table->foreign('program_id', 'program_fk_1001501')->references('id')->on('academic_programs');
            $table->foreign('grade_level_id', 'grade_level_fk_1001502')->references('id')->on('grade_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign('program_fk_1001501');
            $table->dropForeign('grade_level_fk_1001502');
            $table->dropColumn(['program_id', 'grade_level_id', 'section', 'max_students', 'is_active']);
        });
    }
}