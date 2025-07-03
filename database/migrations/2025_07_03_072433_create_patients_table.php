<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_number', 100);
            $table->string('nik', 100);
            $table->string('bpjs_number', 100);
            $table->string('phone_number', 20);
            $table->string('fullname', 100);
            $table->enum('gender', ['F', 'M']);
            $table->date('birthday');
            $table->string('job_title', 100);
            $table->text('address');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
