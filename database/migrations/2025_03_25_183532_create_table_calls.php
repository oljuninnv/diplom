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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['primary', 'technical', 'final'])
                  ->comment('Тип созвона: первичное, техническое, финальное');
            
            $table->string('meeting_link');
            
            $table->date('date');
            $table->time('time');
            
            $table->foreignId('candidate_id')
                  ->constrained('users')
                  ->comment('Кандидат на созвоне');
            
            $table->foreignId('tutor_id')
                  ->nullable()
                  ->constrained('users')
                  ->comment('Тьютор на созвоне (если требуется)');
            
            $table->foreignId('hr_manager_id')
                  ->constrained('users')
                  ->comment('HR-менеджер, организующий созвон');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
