<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public const STATUS_LABELS = [
        'pending'  => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        $query = Review::with(['user', 'property'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', $request->rating))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('comment', 'like', "%{$s}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
                });
            })
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        $reviews = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Review::count(),
            'pending'  => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
            'average'  => Review::averageRating(),
        ];

        $statusLabels = self::STATUS_LABELS;

        return view('admin.reviews.index', compact('reviews', 'stats', 'statusLabels'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'property', 'approvedBy']);

        $statusLabels = self::STATUS_LABELS;

        return view('admin.reviews.show', compact('review', 'statusLabels'));
    }

    public function approve(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $review->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'] ?? $review->admin_notes,
        ]);

        return redirect()->route('admin.reviews.show', $review)
            ->with('success', 'Ulasan disetujui dan akan tampil di landing page.');
    }

    public function reject(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [
            'admin_notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $review->update([
            'status'      => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.reviews.show', $review)
            ->with('success', 'Ulasan ditolak.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}