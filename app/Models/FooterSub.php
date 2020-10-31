<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSub extends Model
{
    protected $table = 'footer_sub';
    protected $fillable = ['footer_main_id', 'title', 'url'];
}
