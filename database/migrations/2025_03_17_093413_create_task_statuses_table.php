<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tutor_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('hr_manager_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('github_repo')->nullable();
            $table->enum('status', ['в процессе', 'на проверке', 'одобрено', 'доработка', 'выполнено', 'провалено']);
            $table->date('end_date')->nullable();
            $table->integer('number_of_requests')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_statuses');
    }
};
