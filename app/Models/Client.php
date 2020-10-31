<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['client_name', 'client_email','client_phone','client_address','client_logo','client_url','target_url'];
}
