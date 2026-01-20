<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
    \DB::table('role_has_permissions')->delete();
\DB::table('model_has_roles')->delete();
\DB::table('model_has_permissions')->delete();
\DB::table('roles')->delete();
\DB::table('permissions')->delete();


        // ============= 1) قائمة الصلاحيات =============
        $perms = [
            // المواقع
            "sites.view","sites.create","sites.edit","sites.delete",

            // المسوقين
            "marketers.view","marketers.create","marketers.edit","marketers.delete",

            // مشاهير الإعلانات
            "influencers.view","influencers.create","influencers.edit","influencers.delete",
            "influencers.add_visit","influencers.recharge_balance",

            // زيارات العملاء
            "visits.view","visits.create","visits.edit","visits.delete",
            "visits.change_date","visits.view_past","visits.edit_past",

            // العمولات
            "commissions.view","commissions.create","commissions.edit","commissions.delete",
            "commissions.set_amount","commissions.upload_invoice","commissions.approve_delivery",
            "commissions.edit_past",

            // موظفين التسويق
            "employees.view","employees.create","employees.edit","employees.delete","employees.assign_group",

            // كشف الحساب الرئيسي
            "ledger.view","ledger.add_capital","ledger.export_excel","ledger.export_csv",

            // تقارير
            "reports.view","reports.export",
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(["name" => $p, "guard_name" => "web"]);
        }

        // ============= 2) إنشاء الأدوار =============
        $admin    = Role::firstOrCreate(["name" => "Admin", "guard_name" => "web"]);
        $manager  = Role::firstOrCreate(["name" => "Marketing Manager", "guard_name" => "web"]);
        $employee = Role::firstOrCreate(["name" => "Employee", "guard_name" => "web"]);

        // ============= 3) توزيع الصلاحيات =============

        // Admin → كل الصلاحيات
        $admin->syncPermissions(Permission::all());

        // Manager → كل شيء ما عدا (تغيير التاريخ + تعديل/حذف الماضي + delete عام)
        $blockedForManager = [
            "visits.change_date","visits.edit_past","commissions.edit_past",
            "sites.delete","marketers.delete","influencers.delete",
            "visits.delete","commissions.delete","employees.delete"
        ];
        $manager->syncPermissions(
            Permission::whereNotIn("name", $blockedForManager)->pluck("name")->toArray()
        );

        // Employee → CRUD محدود (اليوم فقط) + رفع فواتير + إدخال زيارات + تقارير
        $employeeAllowed = [
            // Sites / Marketers عرض فقط
            "sites.view","marketers.view",

            // Influencers
            "influencers.view","influencers.add_visit","influencers.recharge_balance",

            // Visits (اليوم فقط)
            "visits.view","visits.create","visits.edit",

            // Commissions
            "commissions.view","commissions.set_amount","commissions.upload_invoice",

            // Reports
            "reports.view","reports.export",
        ];
        $employee->syncPermissions($employeeAllowed);

        // ============= 4) ربط أدوار بمستخدمين (عدلي الإيميلات) =============
        User::where("email","admin@company.com")->first()?->assignRole("Admin");
        User::where("email","manager@company.com")->first()?->assignRole("Marketing Manager");
        User::where("email","employee@company.com")->first()?->assignRole("Employee");

        // ============= 5) ريست الكاش =============
        Artisan::call("permission:cache-reset");
    }
}