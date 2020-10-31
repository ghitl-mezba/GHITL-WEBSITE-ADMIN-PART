<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Products\BannerRequest;
use Illuminate\Support\Facades\File;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class BannerController extends BaseController
{

    protected $banner = '';

    
    public function __construct(Banner $banner)
    {
        $this->middleware('auth:api');
        $this->banner = $banner;
    }

  
    public function index()
    {
        $banners = $this->banner->latest()->paginate(10);
        return $this->sendResponse($banners, 'Banner list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->banner_image){
            $file_name = time().'.' . explode('/', explode(':', substr($request->banner_image, 0, strpos($request->banner_image, ';')))[1])[1];
            \Image::make($request->banner_image)->save(public_path('upload/banner/').$file_name);
            $request->merge(['banner_image' => $file_name]);
        }
        
        DB::update('update banners set banner_id = 0 where banner_id = ?', [$request->get('banner_id')]);

        $banner = $this->banner->create([
            'banner_id' => $request->get('banner_id'),
            'banner_image' => $file_name,
            'banner_title' => $request->get('banner_title'),
            'banner_sub_title' => $request->get('banner_sub_title'),
            'banner_button_name' => $request->get('banner_button_name'),
            'banner_url' => $request->get('banner_url'),
            'banner_target' => $request->get('banner_target')
        ]);


        return $this->sendResponse($banner, 'Banner Created Successfully');
    }

    public function show($id)
    {
        $banner = $this->banner->findOrFail($id);

        return $this->sendResponse($banner, 'Banner Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $banner = $this->banner->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->banner_image != $request->hidden_image){
            $this->removeImage($banner->banner_image);
            $file_name = time().'.' . explode('/', explode(':', substr($request->banner_image, 0, strpos($request->banner_image, ';')))[1])[1];
            \Image::make($request->banner_image)->save(public_path('upload/banner/').$file_name);
            $request->merge(['banner_image' => $file_name]);
        }            
        
        if($request->hidden_id != $request->banner_id){
            DB::update('update banners set banner_id = 0 where banner_id = ?', [$request->get('banner_id')]);
        }

        $banner->banner_image = $file_name;
        $banner->update($request->all());

        return $this->sendResponse($banner, 'Banner Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $banner = $this->banner->findOrFail($id);
        $this->removeImage($banner->banner_image);

        $banner->delete();

        return $this->sendResponse($banner, 'Banner has been Deleted');
    }

    /*public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);

        return response()->json(['success' => true]);
    }*/
    

    public function removeImage($file_name)
    {  
        File::delete('upload/banner/'.$file_name);
    }
}
