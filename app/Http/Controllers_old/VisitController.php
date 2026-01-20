<?php

namespace App\Http\Controllers;
use App\Models\Visit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitController extends Controller
{
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

        return view('visits.index', compact('breadcrumbs', 'visits_all', 'visits_done', 'visits_pending'));


    }

    public function toggleStatus(Request $request, Visit $visit)
    {
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