<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Products\ProductRequest;
use Illuminate\Support\Facades\File;

use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ProductController extends BaseController
{

    protected $product = '';

    
    public function __construct(Product $product)
    {
        $this->middleware('auth:api');
        $this->product = $product;
    }

  
    public function index()
    {
        $products = $this->product->latest()->with('category', 'sub_category', 'sizes', 'tags')->paginate(10);
        return $this->sendResponse($products, 'Product list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->photo){
            $file_name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('upload/product-primary/').$file_name);
            $request->merge(['photo' => $file_name]);
        }

        $product = $this->product->create([
            'category_id' => $request->get('category_id'),
            'sub_category_id' => $request->get('sub_category_id'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'specification' => $request->get('specification'),
            'price' => $request->get('price'),
            'discount_price' => $request->get('discount_price'),
            'photo' => $file_name
        ]);

        $size_ids = [];
        $tag_ids = [];

        foreach ($request->get('sizes') as $size) {
            $size_ids[] = $size['id'];
        }

        foreach ($request->get('tags') as $tag) {
            $tag_ids[] = $tag['id'];
        }

        $product->sizes()->sync($size_ids);
        $product->tags()->sync($tag_ids);
      
        return $this->sendResponse($product, 'Product Created Successfully');
    }

    public function show($id)
    {
        $product = $this->product->with(['category', 'sub_category', 'sizes', 'tags'])->findOrFail($id);

        return $this->sendResponse($product, 'Product Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $product = $this->product->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->photo != $request->hidden_image){
            $this->removeImage($product->photo);
            $file_name = time().'.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(public_path('upload/product-primary/').$file_name);
            $request->merge(['photo' => $file_name]);
        }            
        
        $product->photo = $file_name;
        $product->update($request->all());

        $size_ids = [];
        $tag_ids = [];

        foreach ($request->get('sizes') as $size) {
            $size_ids[] = $size['id'];
        }

        foreach ($request->get('tags') as $tag) {
            $tag_ids[] = $tag['id'];
        }

        $product->sizes()->sync($size_ids);
        $product->tags()->sync($tag_ids);

        return $this->sendResponse($product, 'Product Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $product = $this->product->findOrFail($id);

        $tag_delete = DB::table('product_tag')->where('product_id', $id)->delete();
        $size_delete = DB::table('product_size')->where('product_id', $id)->delete();
        $this->removeImage($product->photo);

        $product->delete();

        return $this->sendResponse($product, 'Product has been Deleted');
    }

    /*public function upload(Request $request)
    {
        $fileName = time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);

        return response()->json(['success' => true]);
    }*/
    

    public function removeImage($file_name)
    {  
        File::delete('upload/product-primary/'.$file_name);
    }

    public function get_sub_category(Request $request)
    {
        $sub_category = SubCategory::where('category_id', $request->category_id)->get();
        return response()->json($sub_category);
    }
}
