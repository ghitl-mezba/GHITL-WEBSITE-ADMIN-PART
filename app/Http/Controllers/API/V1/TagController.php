<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends BaseController
{
    protected $tag = '';


    public function __construct(Tag $tag)
    {
        $this->middleware('auth:api');
        $this->tag = $tag;
    }


    public function index()
    {
        $tags = $this->tag->latest()->paginate(10);

        return $this->sendResponse($tags, 'Tag list');
    }

    public function list()
    {
        $tags = Tag::all();
        //$tags = $this->tag->pluck('name', 'id');
        return $this->sendResponse($tags, 'Tag list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $tag = "";

        $isExist = Tag::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag = $this->tag->create([
                'name' => $request->get('name'),
            ]);
            $msg = "Tag Created Successfully";
        }

        return $this->sendResponse($tag, $msg);
    }

    
    public function update(Request $request, $id)
    {
        
        $msg = "";
        $tag = $this->tag->findOrFail($id);
        
        $isExist = Tag::select("*")
                        ->where("name", $request->name)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $tag->update($request->all());
            $msg = "Tag Information has been updated";
        }

        return $this->sendResponse($tag, $msg);
    }
}
