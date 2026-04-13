<?php

namespace App\Observers;

use App\Models\BlogCategory;
use App\Observers\Concerns\InterceptsAdminActions;

class BlogCategoryObserver
{
    use InterceptsAdminActions;
}
