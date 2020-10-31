<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['gallery_image', 'gallery_title','gallery_description','gallery_price'];
}
