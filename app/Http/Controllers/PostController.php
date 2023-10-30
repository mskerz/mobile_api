<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    //
    function getAllPost(){
        $posts = Post::orderBy('updated_at', 'desc')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->select('posts.id', 'posts.title', 'posts.details','users.name as username', 'posts.images', 'categories.title as category', 'posts.category_id','posts.created_at', 'posts.updated_at')
            ->get();
         return $posts;
    }

    function getPostbyId($post_id){
        $post = Post::orderBy('created_at', 'desc')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->where('posts.id', $post_id) // Add a condition to filter by post_id
        ->select('posts.id','posts.title', 'posts.details','posts.images','users.name as username' , 'categories.title as category','posts.category_id', 'posts.created_at', 'posts.updated_at')
        ->first();
        return response()->json($post);
    }
    function getAllPostUser(){
        $id =0;
        if (auth()->check()) {
            $id = auth()->user()->id;
            // ทำสิ่งที่คุณต้องการเมื่อผู้ใช้ล็อกอินอยู่
        } else {
            // ทำสิ่งที่คุณต้องการเมื่อไม่มีผู้ใช้ล็อกอิน
        }
        
        $posts = Post::orderBy('updated_at', 'desc')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        ->where('users.id', $id)
        ->select('posts.id','posts.title', 'posts.details','posts.images','users.name as username' , 'categories.title as category',  'posts.category_id','posts.created_at', 'posts.updated_at')
        ->get();

         return response()->json($posts);
    }

    public function getPostsByCategory(Request $request) {
        $category_id = $request->input('category_id');
        $posts = Post::orderBy('updated_at', 'desc')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->where('categories.id', $category_id)
            ->select('posts.id','posts.title', 'posts.details','posts.images','users.name as username' , 'categories.title as category','posts.category_id', 'posts.created_at', 'posts.updated_at')
            ->get();
        return response()->json($posts);
    }
    
     

    public function SearchPost(Request $request) {
        $search = $request->input('search'); 
        $posts = Post::orderBy('created_at', 'desc')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->where(function ($query) use ($search) {
                $query->where('users.name', 'LIKE', "%$search%") // ค้นหาโพสต์จากชื่อผู้เขียน
                    ->orWhere('posts.title', 'LIKE', "%$search%") // ค้นหาโพสต์จากชื่อโพสต์
                    ->orWhere('posts.details', 'LIKE', "%$search%"); // ค้นหาโพสต์จากเนื้อหาในโพสต์
            })
            ->select('posts.id','posts.title', 'posts.details','posts.images','users.name as username' , 'categories.title as category', 'posts.created_at', 'posts.updated_at')
            ->get();
    
        return response()->json($posts);
    }



    function getPostUserByPostId($post_id) {
        $id = 0;
    
        if (auth()->check()) {
            $id = auth()->user()->id;
            // You can include additional logic for authenticated users here.
        } else {
            // You can include logic for unauthenticated users here.
        }
    
        $post = Post::orderBy('created_at', 'desc')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->where('users.id', $id)
            ->where('posts.id', $post_id) // Add a condition to filter by post_id
            ->select('posts.id', 'posts.title', 'posts.details','posts.images' , 'users.name as username' ,'categories.title as category','posts.category_id', 'posts.created_at', 'posts.updated_at')
            ->first(); // Use 'first' to get a single post
    
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
    
        return response()->json($post);
    }
    
    public function createPost(Request $request){
        $now = Carbon::now()->addHours(7);
        $post =Post::create(
            [
                'title'=> $request['title'],
                'details'=> $request['details'],
                "category_id"=> $request['category_id'],
                "user_id"=> auth()->user()->id,
                "images"=> $request['images'],
            ]
            );
        $post->created_at = $now;
        $post->updated_at =  $post->created_at;    
            
        return response()->json(['message' => 'create post success'], 200);
    }
    public function UpdatePost(Request $request,$post_id){
        $post = Post::find($post_id);

         
        $now = Carbon::now()->addHours(7);
        if(!$post){
            return response()->json(["Message"=>"not found Post for Update"],403);
        }
        $post->title = $request->title;
        $post->details = $request->details;
        $post->category_id = $request->category_id;
        $post->user_id = auth()->user()->id;
         
        $post->updated_at = $now;
        $post->save();
    
        return response()->json(['message' => 'edit post success'], 200);
    }
    
    

    public function deletePost(Request $request,$post_id){
        // ค้นหาโพสต์ที่ต้องการลบ
        $post = Post::find($post_id);

        if (!$post) {
            // กรณีไม่พบโพสต์
            return response()->json(['message' => 'โพสต์ไม่พบ'], 404);
        }

        // ตรวจสอบว่าผู้ใช้ที่เข้าสู่ระบบเป็นเจ้าของโพสต์ (ถ้าไม่ใช่ผู้เขียนไม่สามารถลบได้)
        if ($post->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'คุณไม่ได้รับอนุญาตให้ลบโพสต์นี้'], 403); // 403 Forbidden
        }

        // ลบโพสต์แบบซอฟต์
        $post->delete();

        return response()->json(['message' => 'ลบโพสต์เรียบร้อย']);
    }


    public function uploadMultiImage(){

    }
    
}
