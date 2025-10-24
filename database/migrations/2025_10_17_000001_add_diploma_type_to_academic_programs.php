<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDiplomaTypeToAcademicPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify the enum to include 'diploma'
        DB::statement("ALTER TABLE academic_programs MODIFY COLUMN type ENUM('senior_high', 'college', 'diploma') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE academic_programs MODIFY COLUMN type ENUM('senior_high', 'college') NOT NULL");
    }
}
