<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Products\SliderRequest;
use Illuminate\Support\Facades\File;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class SliderController extends BaseController
{

    protected $slider = '';

    
    public function __construct(Slider $slider)
    {
        $this->middleware('auth:api');
        $this->slider = $slider;
    }

  
    public function index()
    {
        $sliders = $this->slider->latest()->paginate(10);
        return $this->sendResponse($sliders, 'Slider list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->slider_image){
            $file_name = time().'.' . explode('/', explode(':', substr($request->slider_image, 0, strpos($request->slider_image, ';')))[1])[1];
            \Image::make($request->slider_image)->save(public_path('upload/slider/').$file_name);
            $request->merge(['slider_image' => $file_name]);
        }

        $slider = $this->slider->create([
            'slider_image' => $file_name,
            'slider_title' => $request->get('slider_title'),
            'slider_sub_title' => $request->get('slider_sub_title'),
            'slider_url' => $request->get('slider_url')
        ]);

        return $this->sendResponse($slider, 'Slider Created Successfully');
    }

    public function show($id)
    {
        $slider = $this->slider->findOrFail($id);

        return $this->sendResponse($slider, 'Slider Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $slider = $this->slider->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->slider_image != $request->hidden_image){
            $this->removeImage($slider->slider_image);
            $file_name = time().'.' . explode('/', explode(':', substr($request->slider_image, 0, strpos($request->slider_image, ';')))[1])[1];
            \Image::make($request->slider_image)->save(public_path('upload/slider/').$file_name);
            $request->merge(['slider_image' => $file_name]);
        }            
        
        $slider->slider_image = $file_name;
        $slider->update($request->all());

        return $this->sendResponse($slider, 'Slider Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $slider = $this->slider->findOrFail($id);
        $this->removeImage($slider->slider_image);

        $slider->delete();

        return $this->sendResponse($slider, 'Slider has been Deleted');
    }

    /*public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);

        return response()->json(['success' => true]);
    }*/
    

    public function removeImage($file_name)
    {  
        File::delete('upload/slider/'.$file_name);
    }
}
