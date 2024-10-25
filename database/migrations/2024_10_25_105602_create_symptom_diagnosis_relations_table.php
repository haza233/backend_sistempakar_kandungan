<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSymptomDiagnosisRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('symptom_diagnosis_relations', function (Blueprint $table) {
            $table->id();
    $table->foreignId('symptom_id')->constrained('symptoms');
    $table->foreignId('diagnosis_id')->constrained('diagnoses');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('symptom_diagnosis_relations');
    }
}
