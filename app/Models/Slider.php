<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['slider_image', 'slider_title','slider_sub_title','slider_url'];
}
