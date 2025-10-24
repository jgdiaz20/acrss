<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradeLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('program_id');
            $table->string('level_name'); // "Grade 11", "Grade 12", "1st Year", "2nd Year", "3rd Year", "4th Year"
            $table->string('level_code'); // "G11", "G12", "1Y", "2Y", "3Y", "4Y"
            $table->integer('level_order'); // 1, 2, 3, 4 for ordering
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('program_id', 'program_fk_1001500')->references('id')->on('academic_programs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grade_levels');
    }
}