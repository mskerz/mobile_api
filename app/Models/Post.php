<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Post extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['title', 'details','category_id','user_id'];
    protected $dates = ['deleted_at'];

    
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // public function bookmarks(){
    //     return $this->belongsToMany(User::class, 'bookmark_user', 'post_id', 'user_id');
    // }
    
}
