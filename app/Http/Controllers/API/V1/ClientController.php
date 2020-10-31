<?php

namespace App\Http\Controllers\API\V1;
use Illuminate\Support\Facades\File;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class ClientController extends BaseController
{

    protected $client = '';

    
    public function __construct(Client $client)
    {
        $this->middleware('auth:api');
        $this->client = $client;
    }

  
    public function index()
    {
        $clients = $this->client->latest()->paginate(10);
        return $this->sendResponse($clients, 'Client list');
    }


    public function store(Request $request)
    {
        $file_name = null;
        if($request->client_logo){
            $file_name = time().'.' . explode('/', explode(':', substr($request->client_logo, 0, strpos($request->client_logo, ';')))[1])[1];
            \Image::make($request->client_logo)->save(public_path('upload/client/').$file_name);
            $request->merge(['client_logo' => $file_name]);
        }

        $client = $this->client->create([
            'client_name' => $request->get('client_name'),
            'client_email' => $request->get('client_email'),
            'client_phone' => $request->get('client_phone'),
            'client_address' => $request->get('client_address'),
            'client_logo' => $file_name,
            'client_url' => $request->get('client_url'),
            'target_url' => $request->get('target_url'),
        ]);

        return $this->sendResponse($client, 'Client Created Successfully');
    }

    public function show($id)
    {
        $client = $this->client->with(['client'])->findOrFail($id);

        return $this->sendResponse($client, 'Client Details');
    }

    public function update(Request $request, $id)
    {
        //find row
        $client = $this->client->findOrFail($id);

        //update new file
        $file_name = null;
        if($request->client_logo != $request->hidden_image){
            $this->removeImage($client->client_logo);
            $file_name = time().'.' . explode('/', explode(':', substr($request->client_logo, 0, strpos($request->client_logo, ';')))[1])[1];
            \Image::make($request->client_logo)->save(public_path('upload/client/').$file_name);
            $request->merge(['client_logo' => $file_name]);
        }            
        
        $client->client_logo = $file_name;
        $client->update($request->all());

        return $this->sendResponse($client, 'Client Information has been updated');
    }

    public function destroy($id)
    {

        $this->authorize('isAdmin');
        
        $client = $this->client->findOrFail($id);
        $this->removeImage($client->client_logo);

        $client->delete();

        return $this->sendResponse($client, 'Client has been Deleted');
    }


    public function removeImage($file_name)
    {  
        File::delete('upload/client/'.$file_name);
    }

}
