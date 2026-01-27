<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Stichoza\GoogleTranslate\GoogleTranslate;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(10);
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الصلاحيات', 'url' => route('permissions.index')],
        ];
        $pageTitle = 'قائمة الصلاحيات';
        return view('permissions.index', compact('permissions', 'breadcrumbs', 'pageTitle'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الصلاحيات', 'url' => route('permissions.index')],
            ['label' => 'إضافة صلاحية', 'url' => route('permissions.create')],
        ];
        $pageTitle = 'إضافة صلاحية جديدة';
        return view('permissions.create', compact('breadcrumbs', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions,name']);

        Permission::create(['name' => $request->name]);
        $name = $request->name;
        $isArabic = preg_match('/\p{Arabic}/u', $name);

        $tr = new GoogleTranslate();

        // Ensure both Arabic and English translations are available for the permission name
        if ($isArabic) {
            $tr->setSource('ar')->setTarget('en');
            $englishName = $tr->translate($name);
            $arabicName  = $name;
        } else {
            $tr->setSource('en')->setTarget('ar');
            $arabicName  = $tr->translate($name);
            $englishName = $name;
        }
        $this->updateLangFile('roles', $englishName, $arabicName);
        return redirect()->route('permissions.index')->with('success', 'تم إنشاء الصلاحية ');
    }

    protected function updateLangFile($file, $key, $value)
    {
        // Create the language file if it doesn't exist to avoid include errors
        $path = resource_path("lang/ar/{$file}.php");

        if (!file_exists($path)) {
            file_put_contents($path, "<?php\n\nreturn [\n];");
        }

        $translations = include($path);
        $translations[$key] = $value;

        $export = var_export($translations, true);
        file_put_contents($path, "<?php\n\nreturn {$export};");
    }

    public function edit(Permission $permission)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'الصلاحيات', 'url' => route('permissions.index')],
            ['label' => 'تعديل صلاحية', 'url' => route('permissions.edit', $permission->id)],
        ];
        $pageTitle = 'تعديل صلاحية';
        return view('permissions.edit', compact('permission', 'breadcrumbs', 'pageTitle'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate(['name' => 'required|unique:permissions,name,' . $permission->id]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'تم تحديث الصلاحية ');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'تم الحذف ');
    }
}
