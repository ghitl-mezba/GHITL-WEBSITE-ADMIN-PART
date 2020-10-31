<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends BaseController
{
    protected $faq = '';


    public function __construct(Faq $faq)
    {
        $this->middleware('auth:api');
        $this->faq = $faq;
    }


    public function index()
    {
        $faqs = $this->faq->latest()->paginate(10);

        return $this->sendResponse($faqs, 'Faq list');
    }

    public function list()
    {
        $faqs = $this->faq->pluck('question', 'answer', 'id');

        return $this->sendResponse($faqs, 'Faq list');
    }


    public function store(Request $request)
    {
        $msg = "";
        $faq_var = "";

        $isExist = Faq::select("*")
                        ->where("question", $request->question)
                        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $faq_var = $this->faq->create([
                'question' => $request->get('question'),
                'answer' => $request->get('answer'),
            ]);
            $msg = "Faq Created Successfully";
        }

        return $this->sendResponse($faq_var, $msg);
    }

    
    public function update(Request $request, $id)
    {
        
        $msg = "";
        $faq = $this->faq->findOrFail($id);
        
        $isExist = Faq::select("*")
        ->where("question", $request->question)
        ->where("answer", $request->answer)
        ->exists();
   
        if ($isExist) {
            $msg = "Already Exist";
        }else{
            $faq->update($request->all());
            $msg = "Faq Information has been updated";
        }

        return $this->sendResponse($faq, $msg);
    }
    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $faq = $this->faq->findOrFail($id);

        $faq->delete();

        return $this->sendResponse($faq, 'FAQ has been Deleted');
    }
}
