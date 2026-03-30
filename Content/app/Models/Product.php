<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['prod_name', 'brand', 'new_cvr', 'used_cvr', 'rewl_cvr', 
	'status', 'created_by', 'updated_by'];
}
