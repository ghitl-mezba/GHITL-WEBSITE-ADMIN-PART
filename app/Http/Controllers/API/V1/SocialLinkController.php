<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class SocialLinkController extends BaseController
{
    protected $social_link = '';


    public function __construct(SocialLink $social_link)
    {
        $this->middleware('auth:api');
        $this->social_link = $social_link;
    }


    public function index()
    {
        $social_links = $this->social_link->latest()->paginate(10);

        return $this->sendResponse($social_links, 'Social Link list');
    }

    public function list()
    {
        $social_links = $this->social_link->pluck('link_icon_name', 'link_title', 'link_url', 'id');

        return $this->sendResponse($social_links, 'Social Link list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $social_link = "";

        $isExist = SocialLink::select("*")
                        ->where("link_icon_name", $request->link_icon_name)
                        ->where("link_title", $request->link_title)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $social_link = $this->social_link->create([
                'link_icon_name' => $request->get('link_icon_name'),
                'link_title' => $request->get('link_title'),
                'link_url' => $request->get('link_url'),
            ]);
            $msg = "Social Link Successfully Created";
        }

        return $this->sendResponse($social_link, $msg);
    }

    
    public function update(Request $request, $id)
    {
        
        $msg = "";
        $social_link = $this->social_link->findOrFail($id);
        
        $isExist = SocialLink::select("*")
        ->where("link_icon_name", $request->link_icon_name)
        ->where("link_title", $request->link_title)
        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $social_link->update($request->all());
            $msg = "Social Link Information has been updated";
        }

        return $this->sendResponse($social_link, $msg);
    }

    
    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $social_link = $this->social_link->findOrFail($id);

        $social_link->delete();

        return $this->sendResponse($social_link, 'Social Link has been Deleted');
    }
}
