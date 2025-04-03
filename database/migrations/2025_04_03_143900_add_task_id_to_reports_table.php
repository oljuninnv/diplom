<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('task_id')->nullable()->after('user_id');
            $table->foreign('task_id')->references('id')->on('task_statuses')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });
    }
};
