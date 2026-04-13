<?php

namespace App\Observers;

use App\Models\Project;
use App\Observers\Concerns\InterceptsAdminActions;

class ProjectObserver
{
    use InterceptsAdminActions;
}
