<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeletedAtColumnsFromRemainingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove deleted_at column from subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from lessons table
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from academic_programs table
        Schema::table('academic_programs', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from grade_levels table
        Schema::table('grade_levels', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add back deleted_at columns (for rollback)
        Schema::table('subjects', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('academic_programs', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('grade_levels', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
    }
}