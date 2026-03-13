<?php

use Illuminate\Database\Migrations\Migration;
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
        if (DB::getDriverName() === 'mysql') {
            // Original MySQL behaviour – expand the ENUM
            DB::statement("ALTER TABLE academic_programs MODIFY COLUMN type ENUM('senior_high', 'college', 'diploma') NOT NULL");
        } elseif (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: type is backed by a check constraint, not a real ENUM
            // 1) Widen column to VARCHAR to be safe
            DB::statement("ALTER TABLE academic_programs ALTER COLUMN type TYPE VARCHAR(20)");

            // 2) Replace the existing check constraint with one that includes 'diploma'
            DB::statement("
                DO $$
                BEGIN
                    -- Drop existing constraint if present
                    IF EXISTS (
                        SELECT 1
                        FROM pg_constraint
                        WHERE conrelid = 'academic_programs'::regclass
                          AND conname = 'academic_programs_type_check'
                    ) THEN
                        ALTER TABLE academic_programs DROP CONSTRAINT academic_programs_type_check;
                    END IF;

                    -- Add new constraint including 'diploma'
                    ALTER TABLE academic_programs
                        ADD CONSTRAINT academic_programs_type_check
                        CHECK (type IN ('senior_high', 'college', 'diploma'));
                END
                $$;
            ");
        } else {
            // Other drivers: do nothing special
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'mysql') {
            // Restore original ENUM for MySQL
            DB::statement("ALTER TABLE academic_programs MODIFY COLUMN type ENUM('senior_high', 'college') NOT NULL");
        } elseif (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: revert the check constraint back to only senior_high/college
            DB::statement("
                DO $$
                BEGIN
                    IF EXISTS (
                        SELECT 1
                        FROM pg_constraint
                        WHERE conrelid = 'academic_programs'::regclass
                          AND conname = 'academic_programs_type_check'
                    ) THEN
                        ALTER TABLE academic_programs DROP CONSTRAINT academic_programs_type_check;
                    END IF;

                    ALTER TABLE academic_programs
                        ADD CONSTRAINT academic_programs_type_check
                        CHECK (type IN ('senior_high', 'college'));
                END
                $$;
            ");
        } else {
            // Other drivers: do nothing special
        }
    }
}