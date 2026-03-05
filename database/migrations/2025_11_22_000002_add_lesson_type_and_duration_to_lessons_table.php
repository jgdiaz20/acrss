<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLessonTypeAndDurationToLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Add lesson type and duration tracking
            $table->enum('lesson_type', ['lecture', 'laboratory'])->default('lecture')->after('subject_id');
            $table->decimal('duration_hours', 4, 2)->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['lesson_type', 'duration_hours']);
        });
    }
}
