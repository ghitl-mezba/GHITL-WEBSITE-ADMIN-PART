<?php

namespace App\Http\Controllers\API\V1;
use Illuminate\Support\Facades\File;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{

    protected $gallery = '';

    
    public function __construct(Gallery $gallery)
    {
        $this->middleware('auth:api');
        $this->gallery = $gallery;
    }

  
    public function index()
    {
        $galleries = $this->gallery->latest()->paginate(10);
        return $this->sendResponse($galleries, 'Gallery list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->gallery_image){
            $file_name = time().'.' . explode('/', explode(':', substr($request->gallery_image, 0, strpos($request->gallery_image, ';')))[1])[1];
            \Image::make($request->gallery_image)->save(public_path('upload/gallery/').$file_name);
            $request->merge(['gallery_image' => $file_name]);
        }

        $gallery = $this->gallery->create([
            'gallery_image' => $file_name,
            'gallery_title' => $request->get('gallery_title'),
            'gallery_description' => $request->get('gallery_description'),
            'gallery_price' => $request->get('gallery_price'),
        ]);


        return $this->sendResponse($gallery, 'gallery Created Successfully');
    }

    public function show($id)
    {
        $gallery = $this->gallery->findOrFail($id);

        return $this->sendResponse($gallery, 'Gallery Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $gallery = $this->gallery->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->gallery_image != $request->hidden_image){
            $this->removeImage($gallery->gallery_image);
            $file_name = time().'.' . explode('/', explode(':', substr($request->gallery_image, 0, strpos($request->gallery_image, ';')))[1])[1];
            \Image::make($request->gallery_image)->save(public_path('upload/gallery/').$file_name);
            $request->merge(['gallery_image' => $file_name]);
        }            
    
        $gallery->gallery_image = $file_name;
        $gallery->update($request->all());

        return $this->sendResponse($gallery, 'Gallery Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $gallery = $this->gallery->findOrFail($id);
        $this->removeImage($gallery->gallery_image);

        $gallery->delete();

        return $this->sendResponse($gallery, 'Gallery has been Deleted');
    }

    /*public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);

        return response()->json(['success' => true]);
    }*/
    

    public function removeImage($file_name)
    {  
        File::delete('upload/gallery/'.$file_name);
    }
}
