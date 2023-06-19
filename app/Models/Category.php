<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Profiler\Profile;

class Category extends Model
{
    use HasFactory;
    protected $fillable=['category_name'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }


}
