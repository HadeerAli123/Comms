<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'المواقع', 'url' => route('sites.index')],
    ];

    $pageTitle = 'المواقع';

    $query = Site::whereNull('parent_id')
        ->withCount(['children as subs_count', 'marketers as marketers_count', 'clients as clients_count']);

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $sites = $query->paginate(10)->withQueryString();

    $parents = Site::whereNull('parent_id')->pluck('name','id');

    return view('sites.index', [
        'sites' => $sites,
        'breadcrumbs' => $breadcrumbs,
        'parents' => $parents,
        'pageTitle' => $pageTitle
    ]);
}
public function subsites(Site $site)
{
    //($site->children()->withCount(['marketers', 'clients'])->get());

    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'المواقع', 'url' => route('sites.index')],
        ['label' => $site->name, 'url' => '#'],
        ['label' => 'المواقع الفرعية', 'url' => '#'],
    ];

    $pageTitle = 'المواقع الفرعية للموقع: ' . $site->name;

    $subsites = $site->children()
        ->withCount(['children as subs_count', 'marketers as marketers_count', 'clients as clients_count'])
        ->get();

    return view('sites.subsites', [
        'site' => $site,
        'subsites' => $subsites,
        'breadcrumbs' => $breadcrumbs,
        'pageTitle' => $pageTitle
    ]);

}


   public function create()
    {
        $parents = Site::whereNull('parent_id')->pluck('name','id');
        return view('sites.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'parent_id' => ['nullable','exists:sites,id'],
        ]);

        $site = Site::create($data);

        if ($site->parent_id) {
            return redirect()->route('sites.subsites.index', $site->parent_id)
                             ->with('success','تم إضافة الموقع الفرعي بنجاح.');
        }

        return redirect()->route('sites.index')
                         ->with('success','تم إضافة الموقع بنجاح.');
    }

    public function show(Site $site)
    {
        $breadcrumbs = [
            ['name'=>'الرئيسية','url'=>route('dashboard')],
            ['name'=>'المواقع','url'=>route('sites.index')],
            ['name'=>$site->name,'url'=>'#'],
        ];
        return view('sites.show', compact('site','breadcrumbs'));
    }

    public function edit(Site $site)
    {
        $parents = Site::whereNull('parent_id')->where('id','!=',$site->id)->pluck('name','id');
        return view('sites.edit', compact('site','parents'));
    }

    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'parent_id' => ['nullable','exists:sites,id'],
        ]);

        $site->update($data);

        return redirect()->route('sites.index')
                         ->with('success','تم تعديل الموقع بنجاح.');
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return back()->with('success','تم حذف الموقع.');
    }
}