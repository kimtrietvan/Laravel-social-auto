<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinterestBoard extends Model
{
    use HasFactory;
    protected $fillable = [
      'pinterest_id', 'board_id', 'board_name'
    ];
    protected $table = 'pinterest_boards';
}
