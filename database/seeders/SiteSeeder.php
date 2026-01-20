<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    public function run()
    {
        // مواقع رئيسية
        $main1 = Site::create(['name' => 'الموقع الرئيسي 1']);
        $main2 = Site::create(['name' => 'الموقع الرئيسي 2']);

        // فروع تحت الموقع الرئيسي 1
        Site::create(['name' => 'فرع 1-1', 'parent_id' => $main1->id]);
        Site::create(['name' => 'فرع 1-2', 'parent_id' => $main1->id]);

        // فروع تحت الموقع الرئيسي 2
        Site::create(['name' => 'فرع 2-1', 'parent_id' => $main2->id]);
        Site::create(['name' => 'فرع 2-2', 'parent_id' => $main2->id]);

        // يمكنك إضافة المزيد هنا...
    }
}