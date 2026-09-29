<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';
    protected $fillable = ['name', 'type', 'url', 'banner', 'is_active'];

    public function getCategory() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
