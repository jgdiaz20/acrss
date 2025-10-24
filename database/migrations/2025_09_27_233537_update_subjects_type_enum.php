<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSubjectsTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For SQLite compatibility, we need to recreate the table
        if (DB::getDriverName() === 'sqlite') {
            // Create a new table with the updated structure
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
            
            // Copy data with type conversion
            DB::statement("INSERT INTO subjects_temp (id, name, code, description, credits, type, requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at) 
                SELECT id, name, code, description, credits,
                    CASE 
                        WHEN type IN ('core', 'practical', 'theoretical') THEN 'major'
                        WHEN type = 'elective' THEN 'minor'
                        ELSE 'major'
                    END as type,
                    requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at 
                FROM subjects");
            
            // Drop old table and rename new one
            Schema::drop('subjects');
            Schema::rename('subjects_temp', 'subjects');
        } else {
            // MySQL/PostgreSQL specific implementation
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('core', 'elective', 'practical', 'theoretical', 'minor', 'major') DEFAULT 'core'");
            DB::statement("UPDATE subjects SET type = 'major' WHERE type IN ('core', 'practical', 'theoretical')");
            DB::statement("UPDATE subjects SET type = 'minor' WHERE type = 'elective'");
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('minor', 'major') DEFAULT 'major'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // For SQLite compatibility, we need to recreate the table
        if (DB::getDriverName() === 'sqlite') {
            // Create a new table with the original structure
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
            
            // Copy data with type conversion
            DB::statement("INSERT INTO subjects_temp (id, name, code, description, credits, type, requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at) 
                SELECT id, name, code, description, credits,
                    CASE 
                        WHEN type = 'major' THEN 'core'
                        WHEN type = 'minor' THEN 'elective'
                        ELSE 'core'
                    END as type,
                    requires_lab, requires_equipment, equipment_requirements, is_active, created_at, updated_at, deleted_at 
                FROM subjects");
            
            // Drop old table and rename new one
            Schema::drop('subjects');
            Schema::rename('subjects_temp', 'subjects');
        } else {
            // MySQL/PostgreSQL specific implementation
            DB::statement("ALTER TABLE subjects MODIFY COLUMN type ENUM('core', 'elective', 'practical', 'theoretical') DEFAULT 'core'");
            DB::statement("UPDATE subjects SET type = 'core' WHERE type = 'major'");
            DB::statement("UPDATE subjects SET type = 'elective' WHERE type = 'minor'");
        }
    }
}
