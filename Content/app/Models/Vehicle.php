<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = ['veh_name', 'varient', 'cc', 'brand', 'seating_cap',
	'status', 'created_by', 'updated_by'];
}
