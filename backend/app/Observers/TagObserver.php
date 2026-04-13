<?php

namespace App\Observers;

use App\Models\Tag;
use App\Observers\Concerns\InterceptsAdminActions;

class TagObserver
{
    use InterceptsAdminActions;
}
