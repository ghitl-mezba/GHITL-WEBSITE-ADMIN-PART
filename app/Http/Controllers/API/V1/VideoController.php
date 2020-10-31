<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Facades\File;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BaseController
{

    protected $video = '';

    
    public function __construct(Video $video)
    {
        $this->middleware('auth:api');
        $this->video = $video;
    }

  
    public function index()
    {
        $videos = $this->video->latest()->paginate(10);
        return $this->sendResponse($videos, 'Video list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->video_image){
            $file_name = time().'.' . explode('/', explode(':', substr($request->video_image, 0, strpos($request->video_image, ';')))[1])[1];
            \Image::make($request->video_image)->save(public_path('upload/video/').$file_name);
            $request->merge(['video_image' => $file_name]);
        }

        $video = $this->video->create([
            'video_image' => $file_name,
            'video_title' => $request->get('video_title'),
            'video_url' => $request->get('video_url'),
            'video_target' => $request->get('video_target'),
        ]);


        return $this->sendResponse($video, 'Video Information Created Successfully');
    }

    public function show($id)
    {
        $video = $this->video->findOrFail($id);

        return $this->sendResponse($video, 'Video Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $video = $this->video->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->video_image != $request->hidden_image){
            $this->removeImage($video->video_image);
            $file_name = time().'.' . explode('/', explode(':', substr($request->video_image, 0, strpos($request->video_image, ';')))[1])[1];
            \Image::make($request->video_image)->save(public_path('upload/video/').$file_name);
            $request->merge(['video_image' => $file_name]);
        }            
    
        $video->video_image = $file_name;
        $video->update($request->all());

        return $this->sendResponse($video, 'Video Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $video = $this->video->findOrFail($id);
        $this->removeImage($video->video_image);

        $video->delete();

        return $this->sendResponse($video, 'Video has been Deleted');
    }

    /*public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);

        return response()->json(['success' => true]);
    }*/
    

    public function removeImage($file_name)
    {  
        File::delete('upload/video/'.$file_name);
    }
}
