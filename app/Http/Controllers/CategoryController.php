<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function getCategories(){
        $categories = Category::orderBy('id','ASC')->limit(10)->get();
        return response()->json($categories);
    }

    public function getCategories_forcreate() {
        // ดึงข้อมูลหมวดหมู่ที่มี id ไม่เท่ากับ 0
        $categories = Category::where('id', '<>', 0)
            ->orderBy('id', 'ASC')
            ->limit(10)
            ->get();
    
        return response()->json($categories);
    }
    public function getCategory($category_id){
        $category = Category::find($category_id);
        return response()->json($category);
    }
    public function createCategory(Request $request){
        $category =Category::create(
            [
                'title'=> $request['title'],
            ]
            );
        return $category;
    }
    public function updateCategory(Request $request){
        $category = Category::find($request['category_id']);
        $category->title = $request->title;
        $category->save();
        return $category;
    }
    public function deleteCategory(Request $request){
        $category = Category::find($request->category_id);
        $category->title =$request->title;
        $category->delete();
        return $category;
    }
}
