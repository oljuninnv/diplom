<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');

            $table->string('resume');

            $table->enum('status', ['ожидание', 'одобрено', 'отклонено'])->default('ожидание');
              
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
