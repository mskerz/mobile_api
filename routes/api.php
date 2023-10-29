<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookmarkContoller;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

 
Route::get('/', function () {    
    return response()->json([ "message" => "Forum" ]);
});





Route::get('/categories', [CategoryController::class, 'getCategories'] );
Route::get('/category/{category_id}', [CategoryController::class, 'getCategory'] );
Route::post('/category/create', [CategoryController::class, 'createCategory'] );
Route::put('/category/update', [CategoryController::class, 'updateCategory'] );
Route::delete('/category/delete', [CategoryController::class, 'deleteCategory'] );



Route::get('/posts',[PostController::class,'getAllPost']);
Route::get('/post/{post_id}',[PostController::class,'getPostbyid']);
Route::get('posts/filter', [PostController::class,'getPostsByCategory']);
Route::get('/search/posts',[PostController::class,'SearchPost']);
// Route::get('posts/user',[PostController::class,'getAllPostUser']);
Route::post('/register',[Controller::class,'register']);
Route::post('/login',[Controller::class,'verifyLogin']);


// Middleware 
Route::group(['middleware'=>['auth:sanctum']],function(){// use token
    Route::get('/user',[Controller::class,'user'] ) ;
    Route::put('/edit-profile',[Controller::class,'editProfile']);
    Route::delete('/delete-user',[Controller::class,'dropUser']);
    Route::put('/change-password',[Controller::class,'changePassword']);
    Route::post('/logout',[Controller::class,'logout']);
    //posts route
    Route::post("/user/post",[PostController::class,'CreatePost']);
    Route::put("/user/edit/{post_id}",[PostController::class,'UpdatePost']);
    Route::delete("/user/delete/{post_id}",[PostController::class,'deletePost']);
    Route::get('/posts/user',[PostController::class,'getAllPostUser']); 
   
    Route::get('/post/user/{post_id}',[PostController::class,'getPostUserByPostId']);
    
    //bookmark route
    Route::get('/bookmarks/user',[BookmarkContoller::class,'getBookmark']);
    Route::post('/bookmarks-toggle/{post_id}',[BookmarkContoller::class,'toggleBookmark']);
    Route::post('/bookmarks/add/{post_id}', [BookmarkContoller::class,'addBookmark']);
    Route::delete('/bookmarks/remove/{post_id}', [BookmarkContoller::class,'removeBookmark']);


});
 
