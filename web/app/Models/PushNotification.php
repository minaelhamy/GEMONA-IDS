<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Role;

class PushNotification extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = "push_notifications";
    protected $fillable = ['role_id', 'user_id', 'title', 'description'];
    protected $casts = [
        'id'          => 'integer',
        'role_id'     => 'integer',
        'user_id'     => 'integer',
        'title'       => 'string',
        'description' => 'string',
    ];

    public function getImageAttribute()
    {
        if (!empty($this->getFirstMediaUrl('pushNotifications'))) {
            return asset($this->getFirstMediaUrl('pushNotifications'));
        }
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
