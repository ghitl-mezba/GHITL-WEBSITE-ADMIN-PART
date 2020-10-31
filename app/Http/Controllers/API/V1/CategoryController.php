<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected $category = '';


    public function __construct(Category $category)
    {
        $this->middleware('auth:api');
        $this->category = $category;
    }


    public function index()
    {
        $categories = $this->category->latest()->paginate(10);

        return $this->sendResponse($categories, 'Category list');
    }

    public function list()
    {
        $categories = $this->category->pluck('name', 'id');

        return $this->sendResponse($categories, 'Category list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $tag = "";

        $isExist = Category::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag = $this->category->create([
                'name' => $request->get('name'),
            ]);
            $msg = "Category Created Successfully";
        }

        return $this->sendResponse($tag, $msg);
    }

    
    public function update(Request $request, $id)
    {
        
        $msg = "";
        $tag = $this->category->findOrFail($id);
        
        $isExist = Category::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag->update($request->all());
            $msg = "Category Information has been updated";
        }

        return $this->sendResponse($tag, $msg);
    }
}
