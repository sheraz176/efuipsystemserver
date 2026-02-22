<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company\CompanyProfile;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.company.index', compact('companies'));
    }

    public function show()
    {
        $companies = CompanyProfile::all();
        return view('superadmin.company.index', compact('companies'));
    }

    public function create()
    {
        return view('superadmin.company.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Add validation rules for each field
        ]);

        CompanyProfile::create($request->all());
        $companies = CompanyProfile::all();
        
        

        return view('superadmin.company.index', compact('companies'));
    }

    public function edit($id)
    {
        $company = CompanyProfile::findOrFail($id);
        return view('superadmin.company.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Add validation rules for each field
        ]);

        $company = CompanyProfile::findOrFail($id);
        $company->update($request->all());

        return redirect()->route('company.index')->with('success', 'Company profile updated successfully.');
    }

    public function destroy($id)
    {
        $company = CompanyProfile::findOrFail($id);
        $company->delete();

        return redirect()->route('superadmin.company.index')->with('success', 'Company profile deleted successfully.');
    }
}
