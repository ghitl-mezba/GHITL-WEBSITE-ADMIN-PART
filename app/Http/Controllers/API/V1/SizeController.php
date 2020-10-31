<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends BaseController
{
    protected $size = '';


    public function __construct(Size $size)
    {
        $this->middleware('auth:api');
        $this->size = $size;
    }


    public function index()
    {
        $sizes = $this->size->latest()->paginate(10);

        return $this->sendResponse($sizes, 'Size list');
    }

    public function list()
    {
        //$sizes = $this->size->pluck('name', 'id');
        
        $sizes = Size::all();
        return $this->sendResponse($sizes, 'Size list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $tag = "";

        $isExist = Size::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag = $this->size->create([
                'name' => $request->get('name'),
            ]);
            $msg = "Size Created Successfully";
        }

        return $this->sendResponse($tag, $msg);
    }

    
    public function update(Request $request, $id)
    {
        
        $msg = "";
        $tag = $this->size->findOrFail($id);
        
        $isExist = Size::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag->update($request->all());
            $msg = "Size Information has been updated";
        }

        return $this->sendResponse($tag, $msg);
    }
}
