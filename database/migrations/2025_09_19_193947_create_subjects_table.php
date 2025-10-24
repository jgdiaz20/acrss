<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // "Computer Programming", "Data Structures", "Culinary Arts"
            $table->string('code')->unique(); // "COMPROG", "DATASTR", "CULINARY"
            $table->text('description')->nullable();
            $table->integer('credits')->default(3); // Credit hours
            $table->enum('type', ['core', 'elective', 'practical', 'theoretical'])->default('core');
            $table->boolean('requires_lab')->default(false); // For lab subjects
            $table->boolean('requires_equipment')->default(false); // For equipment-heavy subjects
            $table->text('equipment_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}