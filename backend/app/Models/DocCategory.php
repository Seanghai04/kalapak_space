<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocCategory extends Model
{
    protected $fillable = ['name'];
    protected $table = 'doc_categories';
}
