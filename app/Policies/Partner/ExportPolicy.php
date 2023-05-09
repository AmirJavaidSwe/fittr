<?php

namespace App\Policies\Partner;

use App\Models\Partner\Export;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExportPolicy
{
    use HandlesAuthorization;

    public function download(User $user, Export $export): bool
    {
        return true;
    }
}
