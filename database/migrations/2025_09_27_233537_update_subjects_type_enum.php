<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSubjectsTypeEnum extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite implementation (unchanged)
            Schema::create('subjects_temp', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->integer('credits')->default(3);
                $table->enum('type', ['minor', 'major'])->default('major');
                $table->boolean('requires_lab')->default(false);
                $table->boolean('requires_equipment')->default(false);
                $table->text('equipment_requirements')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
            
            DB::statement("INSERT INTO subjects_temp SELECT id, name, code, description, credits,
                CASE WHEN type IN ('core', 'practical', 'theoretical') THEN 'major' WHEN type = 'elective' THEN 'minor' ELSE 'major' END,
                requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at FROM subjects");
            
            Schema::drop('subjects');
            Schema::rename('subjects_temp', 'subjects');
        } elseif (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: convert enum-like behaviour to simple VARCHAR and enforce minor/major

            // 1) Drop any existing CHECK constraints on subjects (original enum creates an implicit CHECK)
            DB::statement("
                DO $$
                DECLARE
                    r RECORD;
                BEGIN
                    FOR r IN
                        SELECT conname
                        FROM pg_constraint
                        WHERE conrelid = 'subjects'::regclass
                          AND contype = 'c'
                    LOOP
                        EXECUTE format('ALTER TABLE subjects DROP CONSTRAINT %I', r.conname);
                    END LOOP;
                END
                $$;
            ");

            // 2) Widen the column type
            DB::statement("ALTER TABLE subjects ALTER COLUMN type TYPE VARCHAR(20)");

            // 3) Normalize any existing values (safe even on a fresh DB)
            DB::statement("UPDATE subjects SET type = 'major' WHERE type IN ('core', 'practical', 'theoretical')");
            DB::statement("UPDATE subjects SET type = 'minor' WHERE type = 'elective'");

            // 4) Add a new CHECK constraint that only allows 'minor' and 'major'
            DB::statement("
                DO $$
                BEGIN
                    IF NOT EXISTS (
                        SELECT 1
                        FROM pg_constraint
                        WHERE conrelid = 'subjects'::regclass
                          AND conname = 'subjects_type_check'
                    ) THEN
                        ALTER TABLE subjects
                            ADD CONSTRAINT subjects_type_check CHECK (type IN ('minor', 'major'));
                    END IF;
                END
                $$;
            ");
        } else {
            // MySQL implementation (unchanged)
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('core', 'elective', 'practical', 'theoretical', 'minor', 'major') DEFAULT 'core'");
            DB::statement("UPDATE subjects SET type = 'major' WHERE type IN ('core', 'practical', 'theoretical')");
            DB::statement("UPDATE subjects SET type = 'minor' WHERE type = 'elective'");
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('minor', 'major') DEFAULT 'major'");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite rollback
            Schema::create('subjects_temp', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->integer('credits')->default(3);
                $table->enum('type', ['core', 'elective', 'practical', 'theoretical'])->default('core');
                $table->boolean('requires_lab')->default(false);
                $table->boolean('requires_equipment')->default(false);
                $table->text('equipment_requirements')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
            
            DB::statement("INSERT INTO subjects_temp SELECT id, name, code, description, credits,
                CASE WHEN type = 'major' THEN 'core' WHEN type = 'minor' THEN 'elective' ELSE 'core' END,
                requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at FROM subjects");
            
            Schema::drop('subjects');
            Schema::rename('subjects_temp', 'subjects');
        } elseif (DB::getDriverName() === 'pgsql') {
            // PostgreSQL rollback
            DB::statement("ALTER TABLE subjects DROP CONSTRAINT subjects_type_check");
            DB::statement("UPDATE subjects SET type = 'core' WHERE type = 'major'");
            DB::statement("UPDATE subjects SET type = 'elective' WHERE type = 'minor'");
            DB::statement("ALTER TABLE subjects ADD CONSTRAINT subjects_type_check CHECK (type IN ('core', 'elective', 'practical', 'theoretical'))");
        } else {
            // MySQL rollback
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('core', 'elective', 'practical', 'theoretical', 'minor', 'major') DEFAULT 'core'");
            DB::statement("UPDATE subjects SET type = 'core' WHERE type = 'major'");
            DB::statement("UPDATE subjects SET type = 'elective' WHERE type = 'minor'");
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('core', 'elective', 'practical', 'theoretical') DEFAULT 'core'");
        }
    }
}