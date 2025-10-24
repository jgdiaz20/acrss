<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_programs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // "Computer Engineering", "Hospitality Management", "STEM", "ABM", "HUMSS"
            $table->string('code')->unique(); // "CE", "HM", "STEM", "ABM", "HUMSS"
            $table->enum('type', ['senior_high', 'college']); // Senior High School or College
            $table->integer('duration_years')->default(2); // 2 for SHS, 4 for College
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_programs');
    }
}