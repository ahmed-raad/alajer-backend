<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAuthorNameAttribute()
    {
        $user = User::find($this->user_id);
        return $user->fullname;
    }

    public function getAuthorJobAttribute()
    {
        $user = User::find($this->user_id);
        return $user->job;
    }

    public function getAuthorImgAttribute()
    {
        $user = User::find($this->user_id);
        $image_name = $user->image;
        return asset('http://localhost:8000/storage/users/' . $image_name);
    }
}
