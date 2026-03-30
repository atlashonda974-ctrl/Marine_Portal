<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelYear extends Model
{
    use HasFactory;

    protected $fillable = ['modelyr', 'status', 'created_by', 'updated_by'];
}
