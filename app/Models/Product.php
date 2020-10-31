<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //use SoftDeletes;

    
    protected $fillable = [
        'category_id', 'sub_category_id', 'name', 'description', 'specification', 'price','discount_price','photo'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['name as text','id']);
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class)->select(['name as text','id']);
    }
}
