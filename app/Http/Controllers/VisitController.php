<?php

namespace App\Http\Controllers;
use App\Models\Visit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
class VisitController extends BaseController

{
    use AuthorizesRequests, ValidatesRequests;
public function __construct()
    {
       $this->middleware('permission:visits.view')->only('index');

        // إعلان/رفض زيارة (تعديل حالة)
        // يسمح لأيٍّ من الصلاحيتين (اليوم أو الماضي) مبدئيًا، وبنفلتر بدقة داخل الدالة حسب التاريخ
        $this->middleware('permission:visits.edit_today|visits.edit_past')->only('toggleStatus');
    }
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'مشاهير المسوقين', 'url' => route('influencers.index')],
            ['label' => 'الزيارات', 'url' => route('visits.index')],
        ];
        $visits_all = Visit::with(['influencer', 'user'])->latest()->get();
        $visits_done = Visit::with(['influencer', 'user'])->where('is_announced', true)->latest()->get();
        $visits_pending = Visit::with(['influencer', 'user'])->where('is_announced', false)->latest()->get();
        $visits_not_specified = Visit::with(['influencer', 'user'])->where('is_announced',0)->latest()->get();
        return view('visits.index', compact('breadcrumbs', 'visits_all', 'visits_done', 'visits_pending','visits_not_specified'));


    }

    public function toggleStatus(Request $request, Visit $visit)
    {
        $user = $request->user();

if ($visit->created_at->isToday()) {
    // اليوم: Admin أو Marketing Manager أو Employee مسموح لهم
    if (! $user->hasAnyRole(['Admin', 'Marketing Manager', 'Employee'])) {
        abort(403, 'ليس لديك صلاحية تعديل زيارات اليوم.');
    }
} else {
    // الماضي: Admin فقط
    if (! $user->hasRole('Admin')) {
        abort(403, 'ليس لديك صلاحية تعديل زيارات سابقة.');
    }
}

        $validated = $request->validate([
            'is_announced' => 'required|in:1,2',
            'rating' => 'nullable|integer|min:1|max:10',
            'accept_notes' => 'nullable|string|max:1000',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480',
        ]);

        $data = [
            'is_announced' => $validated['is_announced'],
        ];

        if ($validated['is_announced'] == 1) {
            $data['rating'] = $validated['rating'] ?? null;
            $data['accept_notes'] = $validated['accept_notes'] ?? null;

            if ($request->hasFile('media')) {
                $mediaPath = $request->file('media')->store('visits_media', 'public');
                $data['media'] = $mediaPath;
            }
        }

        $visit->update($data);

        return back()->with('success', 'تم تحديث حالة الإعلان بنجاح');
    }
}
