<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doc extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'categories',
        'category_order',
        'order_num',
        'status',
        'author_id',
        'parent_id',
        'doc_menu_id',
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(Doc::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Doc::class, 'parent_id')->orderBy('order_num');
    }

    public function docMenu()
    {
        return $this->belongsTo(DocMenu::class, 'doc_menu_id');
    }

    public function sections()
    {
        return $this->hasMany(DocSection::class)->orderBy('order_num');
    }
}
