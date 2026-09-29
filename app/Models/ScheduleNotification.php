<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNotification extends Model
{
    use HasFactory;
    protected $table = 'schedule_notification';
    protected $fillable = ['template_id', 'title', 'description', 'image', 'date', 'time', 'is_active', 'is_sent'];
    
    public function getTemplate() {
        return $this->hasOne(AnimatedTemplate::class, 'id', 'template_id');
    }
}
