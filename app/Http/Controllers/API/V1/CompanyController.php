<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CompanyController extends BaseController
{
    protected $Company = '';


    public function __construct(Company $Company)
    {
        $this->middleware('auth:api');
        $this->Company = $Company;
    }


    public function index()
    {
        $companies = $this->Company->first();
        return $this->sendResponse($companies, 'Company list');
    }

    public function store(Request $request)
    {
        $msg = "";
        $company = "";
        $isExist = Company::select("*")
            ->where("name", $request->name)
            ->exists();

        if ($isExist) {
            $msg = "Already Exist";
        } else {
            if ($request->logo) {
                $file_name = time() . '.' . explode('/', explode(':', substr($request->logo, 0, strpos($request->logo, ';')))[1])[1];
                \Image::make($request->logo)->save(public_path('upload/company/') . $file_name);
                $request->merge(['logo' => $file_name]);
            }
            $company = $this->Company->create([
                'name' => $request->get('name'),
                'address' => $request->get('address'),
                'email' => $request->get('email'),
                'web' => $request->get('web'),
                'mobile' => $request->get('mobile'),
                'phone' => $request->get('phone'),
                'fax' => $request->get('fax'),
                'office_time' => $request->get('office_time'),
                'logo' => $request->logo,

            ]);
            $msg = "Company Created Successfully";
        }
        // dd($msg);
        return $this->sendResponse($company, $msg);
    }

    public function update(Request $request, $id)
    {
        $msg = "";
        $company = $this->Company->findOrFail($id);
        // dd($request);
        $file_name = null;
        if ($request->logo != $request->hidden_image) {
            \File::delete('upload/company/' . $company->logo);
            $file_name = time() . '.' . explode('/', explode(':', substr($request->logo, 0, strpos($request->logo, ';')))[1])[1];
            \Image::make($request->logo)->save(public_path('upload/company/') . $file_name);
            $request->merge(['logo' => $file_name]);
        }

        $company->logo = $file_name;

        $company->update($request->all());
        $msg = "Company Information has been updated";

        return $this->sendResponse($company, $msg);
    }

    public function destroy($id)
    {

        $company = $this->Company->findOrFail($id);

        $company->delete();

        return $this->sendResponse($company, 'Company has been Deleted');
    }
}
