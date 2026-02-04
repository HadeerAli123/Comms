<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Stichoza\GoogleTranslate\GoogleTranslate;

class RoleController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'مجموعات العمل', 'url' => route('roles.index')],
        ];

        $pageTitle = 'مجموعات العمل';
        $roles = Role::with('permissions')->paginate(10);
        return view('roles.index', compact('roles', 'breadcrumbs', 'pageTitle'));
    }

    public function create()
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'مجموعات العمل', 'url' => route('roles.index')],
            ['label' => 'إنشاء مجموعة عمل', 'url' => route('roles.create')],
        ];
        $pageTitle = 'إنشاء مجموعة عمل';
        $permissions = Permission::all();
        return view('roles.create', compact('permissions', 'breadcrumbs', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);

        $roleName = $request->name;

        $isArabic = preg_match('/\p{Arabic}/u', $roleName);

        $tr = new GoogleTranslate();

        // Ensure both English and Arabic names are available for translation and localization
        if ($isArabic) {
            $tr->setSource('ar')->setTarget('en');
            $englishName = $tr->translate($roleName);
            $arabicName  = $roleName;
        } else {
            $tr->setSource('en')->setTarget('ar');
            $englishName = $roleName;
            $arabicName  = $tr->translate($roleName);
        }

        $role = Role::create(['name' => $englishName]);

        $role->syncPermissions($request->permissions ?? []);

        $this->updateLangFile('roles', $englishName, $arabicName);

        return redirect()
            ->route('roles.index')
            ->with('success', "تم إنشاء المجموعة ($arabicName) بنجاح");
    }

    protected function updateLangFile($file, $key, $value)
    {
        $path = resource_path("lang/ar/{$file}.php");

        // Create the language file if it doesn't exist to avoid file not found errors
        if (!file_exists($path)) {
            file_put_contents($path, "<?php\n\nreturn [\n];");
        }

        $translations = include($path);
        $translations[$key] = $value;

        $export = var_export($translations, true);
        file_put_contents($path, "<?php\n\nreturn {$export};");
    }

    public function edit(Role $role)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'مجموعات العمل', 'url' => route('roles.index')],
            ['label' => 'تعديل مجموعة عمل', 'url' => route('roles.edit', $role->id)],
        ];
        $pageTitle = 'تعديل مجموعة عمل';
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions', 'breadcrumbs', 'pageTitle'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'تم تحديث المجموعة ');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'تم الحذف ');
    }
}
