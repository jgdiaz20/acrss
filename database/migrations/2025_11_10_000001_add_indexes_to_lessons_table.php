<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Add indexes for foreign keys (improves join performance)
            $table->index('teacher_id', 'lessons_teacher_id_index');
            $table->index('class_id', 'lessons_class_id_index');
            $table->index('room_id', 'lessons_room_id_index');
            $table->index('subject_id', 'lessons_subject_id_index');
            
            // Add index for weekday (frequently used in queries)
            $table->index('weekday', 'lessons_weekday_index');
            
            // Add composite index for conflict detection queries
            // This dramatically speeds up time-based conflict checks
            $table->index(['weekday', 'start_time', 'end_time'], 'lessons_schedule_index');
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
            $table->dropIndex('lessons_teacher_id_index');
            $table->dropIndex('lessons_class_id_index');
            $table->dropIndex('lessons_room_id_index');
            $table->dropIndex('lessons_subject_id_index');
            $table->dropIndex('lessons_weekday_index');
            $table->dropIndex('lessons_schedule_index');
        });
    }
}
