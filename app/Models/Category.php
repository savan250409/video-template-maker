<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [ 'type', 'template_type', 'sort_order', 'name', 'logo', 'is_active' ];
    protected $appends = ['thumbnail'];

    public function getThumbnailAttribute() {
        return 'uploads/category/'.$this->type.'/thumbnail/'. $this->logo;
    }
    
    public function templates() {
        return $this->hasMany(AnimatedTemplate::class, 'category_id', 'id');
    }
    
}
