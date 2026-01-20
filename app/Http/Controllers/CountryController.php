<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
class CountryController extends BaseController
{
       use AuthorizesRequests, ValidatesRequests;
public function __construct()
    {
        $this->middleware('role:Admin'); 
    }
    public function index()
    {
        $pageTitle = 'قائمة الدول';
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الدول', 'url' => route('countries.index')],
        ];
        $countries = Country::latest()->paginate(10);
        return view('countries.index', compact('countries', 'breadcrumbs', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = 'إضافة دولة جديدة';
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الدول', 'url' => route('countries.index')],
            ['label' => 'إضافة دولة', 'url' => route('countries.create')],
        ];
        return view('countries.create', compact('breadcrumbs', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
        ]);

        Country::create($request->only('name'));

        return redirect()->route('countries.index')->with('success', 'تم إضافة الدولة بنجاح.');
    }

    public function edit(Country $country)
    {
        $pageTitle = 'تعديل الدولة';
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الدول', 'url' => route('countries.index')],
            ['label' => 'تعديل الدولة', 'url' => route('countries.edit', $country->id)],
        ];
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
        ]);

        $country->update($request->only('name'));

        return redirect()->route('countries.index')->with('success', 'تم تحديث الدولة بنجاح.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')->with('success', 'تم حذف الدولة بنجاح.');
    }
}
