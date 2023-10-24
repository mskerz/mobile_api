<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Bookmark extends Model
{
    use HasFactory;
   
    protected $table = ['bookmarks_user'];
    protected $fillable = ['user_id','post_id','category'];
    protected $attributes = [
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    

     
}
