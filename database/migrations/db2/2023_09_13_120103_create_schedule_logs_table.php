<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('logging_db')->create('schedule_logs', function (Blueprint $table) {
            $table->id();
            $table->string('jobs_id', 7);
            $table->string('brands');
            $table->string('job_type');
            $table->enum('job_group',['cron','import','export','queue','command']);
            $table->text('data');
            $table->dateTime('fetched');
            $table->dateTime('completed');
            $table->text('failure_message')->nullable();
            $table->enum('status', ['scheduled', 'running', 'completed','cancelled', 'retry', 'failed']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_logs');
    }
};
