<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;
    protected $table = 'admin_notifications';
    protected $fillable = ['user_id', 'template_id', 'clicked'];

    public function getAnimatedTemplate() {
        return $this->hasOne(AnimatedTemplate::class, 'id', 'template_id');
    }
    public function getUser() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}