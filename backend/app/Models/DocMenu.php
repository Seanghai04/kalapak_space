<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocMenu extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order_num', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(DocMenu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DocMenu::class, 'parent_id')->orderBy('order_num')->orderBy('name');
    }

    public function pages()
    {
        return $this->hasMany(Doc::class, 'doc_menu_id');
    }
}
