<?php

namespace App\Actions;

use App\Models\User;


class GetId
{

    public function execute($id): string
    {
        \Log::info($id);

        $user = User::find($id);
        \Log::info($user);
        return 'Отчёт успешно сформирован.';
    }
}