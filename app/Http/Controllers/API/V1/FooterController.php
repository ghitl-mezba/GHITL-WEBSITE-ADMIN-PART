<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\FooterMain;
use App\Models\FooterSub;

use Illuminate\Http\Request;

class FooterController extends BaseController
{
    protected $main_footer = '';
    protected $sub_footer = '';


    public function __construct(FooterMain $main_footer, FooterSub $sub_footer)
    {
        $this->middleware('auth:api');
        $this->main_footer = $main_footer;
        $this->sub_footer = $sub_footer;
    }

    public function index()
    {
        $footer = $this->main_footer->latest()->paginate(10);
        // $footer = FooterMain::select(
        //     'footer_main.id as footer_main_id',
        //     'footer_sub.id as  footer_sub_id',
        //     'footer_main.title as footer_main_title',
        //     'footer_sub.title as footer_sub_title',
        //     'url'
        // )
        //     ->join('footer_sub', 'footer_main.id', '=', 'footer_sub.footer_main_id')

        //     ->paginate(10);

        return $this->sendResponse($footer, 'Footer list');
    }

    public function store(Request $request)
    {
        $msg = "";
        $footer = "";
        // dd($request->inputs);
        $isExist = FooterMain::select("*")
            ->where("title", $request->title)
            ->exists();

        if ($isExist) {
            $msg = "Already Exist";
        } else {
            $footer = $this->main_footer->create([
                'title' => $request->get('title'),
            ]);
            foreach ($request->inputs as $sub_footers) {
                FooterSub::create([
                    "footer_main_id" => $footer->id,
                    "title" => $sub_footers["title"],
                    "url" => $sub_footers["url"],

                ]);
            }
            $msg = "Footer Created Successfully";
        }

        return $this->sendResponse($footer, $msg);
    }

    public function update(Request $request, $id)
    {
        $msg = "";
        $main_footer = $this->main_footer->findOrFail($id);
        $main_footer->where('id', $id)
            ->update(["title" => $request->title]);
        foreach ($request->inputs as $sub_footers) {
            if (!empty($sub_footers["id"])) {
                // FooterSub::where('id', $sub_footers["id"])
                //     ->update([
                //         "footer_main_id" => $id,
                //         "title" => $sub_footers["title"],
                //         "url" => $sub_footers["url"],
                //     ]);

                FooterSub::where('id', $sub_footers["id"])

                    ->update([
                        "footer_main_id" => $id,
                        "title" => $sub_footers["title"],
                        "url" => $sub_footers["url"],
                    ]);
            } else {
                FooterSub::create([
                    "footer_main_id" => $id,
                    "title" => $sub_footers["title"],
                    "url" => $sub_footers["url"],

                ]);
            }
        }
        $msg = "Footer Information has been updated";
        return $this->sendResponse($main_footer, $msg);
    }

    public function destroy($id)
    {

        // $this->authorize('isAdmin');

        $main_footer = $this->main_footer->findOrFail($id);

        $main_footer->where('id', $id)->delete();
        FooterSub::where('footer_main_id', $id)->delete();
        return $this->sendResponse($main_footer, 'Footer has been Deleted');
    }

    public function show($id)
    {
        $footer_sub = FooterSub::where('footer_main_id', $id)->get();

        return $this->sendResponse($footer_sub, 'Edit');
    }

    public function removeSubFooter(Request $request)
    {
        FooterSub::where('id', $request->id)->delete();
    }
}
