<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;
use Illuminate\Http\Request;

class BookmarkContoller extends Controller
{
    //

    public function getBookmark(){
        $id =0;
        if (auth()->check()) {
            $id = auth()->user()->id;
            // ทำสิ่งที่คุณต้องการเมื่อผู้ใช้ล็อกอินอยู่
        } else {
            // ทำสิ่งที่คุณต้องการเมื่อไม่มีผู้ใช้ล็อกอิน
        }
        
        $posts = Post::orderBy('updated_at', 'desc')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('bookmarks_user', 'posts.id', '=','bookmarks_user.post_id')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->where('bookmarks_user.user_id', $id)
        ->select('posts.id','posts.title', 'posts.details','posts.images' ,'users.name as username' , 'categories.title as category', 'posts.created_at', 'posts.updated_at')
        ->get();

         return response()->json($posts);
    }
    public function addBookmark($post_id) {
        $user = auth()->user();
        $post = Post::find($post_id);
        $user->bookmarks()->attach($post_id, ['category' => $post->category_id]);

        return response()->json(['message' => 'บุ๊คมาร์กเรียบร้อย']);
    }
    public function removeBookmark($post_id) {
        $user = auth()->user();
        $user->bookmarks()->detach($post_id);

        return response()->json(['message' => 'ลบบุ๊คมาร์กเรียบร้อย']);
    }

    public function toggleBookmark($post_id){
        $user = auth()->user();
        $bookmarked = $user->bookmarks()->where('post_id', $post_id)->exists();
        if($bookmarked){
            $user->bookmarks()->detach($post_id);
            $message = 'ลบบุ๊คมาร์กเรียบร้อย';
        }else{
            $post = Post::find($post_id);
            $user->bookmarks()->attach($post_id, ['category' => $post->category_id]);
            $message = 'เพิ่มบุ๊คมาร์กเรียบร้อย';
        }
        return response()->json(['message' => $message]);
    }
     
}
