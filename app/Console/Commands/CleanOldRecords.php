<?php

namespace App\Console\Commands;

use App\Enums\UserRoleEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;

class CleanOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:old-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление старых данных';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $currentDate = Carbon::now();

        // Удаление старых Application
        $appCount = DB::table('applications')
            ->where('updated_at', '<', $sixMonthsAgo)
            ->delete();
        $this->info("Deleted {$appCount} old applications.");

        // Удаление старых Call
        $callCount = DB::table('calls')
            ->where('updated_at', '<', $currentDate)
            ->delete();
        $this->info("Deleted {$callCount} old calls.");

        // Удаление старых TaskStatus
        $taskStatusCount = DB::table('task_statuses')
            ->where('end_date', '<', $sixMonthsAgo)
            ->delete();
        $this->info("Deleted {$taskStatusCount} old task statuses.");

        // Удаление старых Report
        $reportCount = DB::table('reports')
            ->where('created_at', '<', $sixMonthsAgo)
            ->delete();
        $this->info("Deleted {$reportCount} old reports.");

        // Удаление неактивных User с ролью User
        $userRoleId = Role::where('name', UserRoleEnum::USER->value)->value('id');
        
        $userCount = User::where('date_of_auth', '<', $sixMonthsAgo)
            ->where('role_id', $userRoleId)
            ->delete();
        $this->info("Deleted {$userCount} inactive users.");

        $this->info('Old records cleanup completed successfully.');

        return 0;
    }
}