<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreditSystemToSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Add new credit system fields
            $table->unsignedInteger('lecture_units')->default(0)->after('credits');
            $table->unsignedInteger('lab_units')->default(0)->after('lecture_units');
            $table->enum('scheduling_mode', ['lab', 'lecture', 'flexible'])->default('flexible')->after('lab_units');
            
            // Add index for scheduling_mode for better query performance
            $table->index('scheduling_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop index using raw SQL to avoid errors if it doesn't exist
        \DB::statement('ALTER TABLE subjects DROP INDEX IF EXISTS subjects_scheduling_mode_index');
        
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['lecture_units', 'lab_units', 'scheduling_mode']);
        });
    }
}
