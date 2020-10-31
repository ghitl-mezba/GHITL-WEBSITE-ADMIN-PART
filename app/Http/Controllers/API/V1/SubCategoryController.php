<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends BaseController
{
    protected $sub_category = '';


    public function __construct(SubCategory $sub_category)
    {
        $this->middleware('auth:api');
        $this->sub_category = $sub_category;
    }


    public function index()
    {
        $sub_categories = $this->sub_category->latest()->with('category')->paginate(10);

        return $this->sendResponse($sub_categories, 'Sub Category list');
    }

    public function list()
    {
        $sub_categories = $this->sub_category->pluck('sub_category_name', 'id');

        return $this->sendResponse($sub_categories, 'Sub Category list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $tag = "";

        $isExist = SubCategory::select("*")
            ->where("sub_category_name", $request->sub_category_name)
            ->where("category_id", $request->category_id)
            ->exists();

        if ($isExist) {
            $msg = "Already Exist";
        } else {
            $tag = $this->sub_category->create([
                'category_id' => $request->get('category_id'),
                'sub_category_name' => $request->get('sub_category_name'),
            ]);
            $msg = "Sub Category Created Successfully";
        }

        return $this->sendResponse($tag, $msg);
    }


    public function update(Request $request, $id)
    {

        $msg = "";
        $tag = $this->sub_category->findOrFail($id);

        $isExist = SubCategory::select("*")
            ->where("sub_category_name", $request->sub_category_name)
            ->where("category_id", $request->category_id)
            ->exists();

        if ($isExist) {
            $msg = "Already Exist";
        } else {
            $tag->update($request->all());
            $msg = "Sub Category Information has been updated";
        }

        return $this->sendResponse($tag, $msg);
    }
}
