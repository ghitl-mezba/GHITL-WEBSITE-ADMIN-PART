<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['banner_id', 'banner_image','banner_title','banner_sub_title','banner_button_name','banner_url','banner_target'];
}
