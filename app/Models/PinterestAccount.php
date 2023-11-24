<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinterestAccount extends Model
{
    use HasFactory;
    protected $fillable = [
      'username', 'cookie', 'user_id', 'proxy'
    ];
    protected $table = 'pinterest_account';
}
