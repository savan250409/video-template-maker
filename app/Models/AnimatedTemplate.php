<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{ Category };

class AnimatedTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'category_id', 'title', 'total_image_count', 'total_editable', 'zip', 'thumbnail', 'json', 'tags', 'is_paid', 'is_active', 'total_views', 'type', 'json', 'zip_folder', 'height', 'width', 'zip_original_name'];
    
    public function category() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
