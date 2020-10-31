<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    protected $fillable = ['name', 'email', 'address', 'fax', 'logo', 'mobile', 'phone', 'web', 'office_time'];
}
