<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    //protected $guarded =[];
    protected $table = 'companies';
    protected $fillable = [
        'name',
        'address_id',
        'category_id',
        'logo',
        'description',
        'type',
        'role_id',
       
        
    ];
    public $timestamps=false;
  
}
