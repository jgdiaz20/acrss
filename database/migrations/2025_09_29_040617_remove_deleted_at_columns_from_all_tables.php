<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeletedAtColumnsFromAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove deleted_at column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from school_classes table
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from rooms table
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        // Remove deleted_at column from permissions table
        Schema::table('permissions', function (Blueprint $table) {
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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable();
        });
    }
}