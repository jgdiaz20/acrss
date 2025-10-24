<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('subject_id');
            $table->boolean('is_primary')->default(false); // Primary subject for the teacher
            $table->integer('experience_years')->nullable(); // Years of experience with this subject
            $table->text('notes')->nullable(); // Additional notes about teacher-subject assignment
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('teacher_id', 'teacher_subject_teacher_fk')->references('id')->on('users');
            $table->foreign('subject_id', 'teacher_subject_subject_fk')->references('id')->on('subjects');
            
            // Ensure unique teacher-subject combinations
            $table->unique(['teacher_id', 'subject_id'], 'teacher_subject_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_subjects');
    }
}