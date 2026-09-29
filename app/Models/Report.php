<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'report';
    protected $fillable = ['template_id', 'email', 'report', 'is_active', 'message'];
    
    public function getTemplate() {
        return $this->hasOne(AnimatedTemplate::class, 'id', 'template_id');
    }
}
