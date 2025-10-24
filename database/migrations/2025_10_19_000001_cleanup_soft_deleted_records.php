<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CleanupSoftDeletedRecords extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration permanently removes all soft-deleted records from tables
     * that previously used soft deletes but now use hard deletes.
     *
     * @return void
     */
    public function up()
    {
        // Clean up soft-deleted school classes (including phantom "Test sesction (B)")
        $deletedClasses = DB::table('school_classes')
            ->whereNotNull('deleted_at')
            ->get();
        
        if ($deletedClasses->count() > 0) {
            echo "Found {$deletedClasses->count()} soft-deleted school classes:\n";
            foreach ($deletedClasses as $class) {
                echo "  - ID: {$class->id}, Name: {$class->name}, Section: {$class->section}, Deleted: {$class->deleted_at}\n";
            }
            
            // Permanently delete soft-deleted school classes
            DB::table('school_classes')
                ->whereNotNull('deleted_at')
                ->delete();
            
            echo "✓ Permanently deleted {$deletedClasses->count()} soft-deleted school classes\n";
        } else {
            echo "✓ No soft-deleted school classes found\n";
        }
        
        // Clean up soft-deleted academic programs
        $deletedPrograms = DB::table('academic_programs')
            ->whereNotNull('deleted_at')
            ->get();
        
        if ($deletedPrograms->count() > 0) {
            echo "Found {$deletedPrograms->count()} soft-deleted academic programs:\n";
            foreach ($deletedPrograms as $program) {
                echo "  - ID: {$program->id}, Name: {$program->name}, Deleted: {$program->deleted_at}\n";
            }
            
            // Permanently delete soft-deleted academic programs
            DB::table('academic_programs')
                ->whereNotNull('deleted_at')
                ->delete();
            
            echo "✓ Permanently deleted {$deletedPrograms->count()} soft-deleted academic programs\n";
        } else {
            echo "✓ No soft-deleted academic programs found\n";
        }
        
        // Clean up soft-deleted grade levels
        $deletedGradeLevels = DB::table('grade_levels')
            ->whereNotNull('deleted_at')
            ->get();
        
        if ($deletedGradeLevels->count() > 0) {
            echo "Found {$deletedGradeLevels->count()} soft-deleted grade levels:\n";
            foreach ($deletedGradeLevels as $level) {
                echo "  - ID: {$level->id}, Name: {$level->level_name}, Deleted: {$level->deleted_at}\n";
            }
            
            // Permanently delete soft-deleted grade levels
            DB::table('grade_levels')
                ->whereNotNull('deleted_at')
                ->delete();
            
            echo "✓ Permanently deleted {$deletedGradeLevels->count()} soft-deleted grade levels\n";
        } else {
            echo "✓ No soft-deleted grade levels found\n";
        }
        
        echo "\n=== Cleanup Complete ===\n";
        echo "All soft-deleted records have been permanently removed.\n";
        echo "The system now uses hard deletes for all entities.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse permanent deletion
        echo "WARNING: This migration cannot be reversed. Soft-deleted records have been permanently removed.\n";
    }
}
