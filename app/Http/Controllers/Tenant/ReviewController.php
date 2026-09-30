<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Form ulasan / view ulasan yang udah ada.
     */
    public function index()
    {
        $user = auth()->user();
        $review = Review::where('user_id', $user->id)->first();

        // Rata-rata rating kos
        $averageRating = Review::averageRating();
        $totalReviews = Review::totalApproved();

        return view('tenant.reviews.index', compact('review', 'averageRating', 'totalReviews'));
    }

    /**
     * Simpan ulasan baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek: 1 user cuma boleh 1 ulasan
        if (Review::where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Anda sudah pernah memberi ulasan. Silakan edit ulasan Anda.');
        }

        $validated = $request->validate([
            'rating'              => ['required', 'integer', 'min:1', 'max:5'],
            'rating_cleanliness'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_security'     => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_facilities'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_price'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_friendliness' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment'             => ['required', 'string', 'min:10', 'max:500'],
            'is_anonymous'        => ['boolean'],
        ], [
            'rating.required'  => 'Rating wajib diisi.',
            'rating.min'       => 'Rating minimal 1 bintang.',
            'rating.max'       => 'Rating maksimal 5 bintang.',
            'comment.required' => 'Komentar wajib diisi.',
            'comment.min'      => 'Komentar minimal 10 karakter.',
            'comment.max'      => 'Komentar maksimal 500 karakter.',
        ]);

        $property = Property::where('is_active', true)->first();

        Review::create([
            'user_id'             => $user->id,
            'property_id'         => $property?->id,
            'rating'              => $validated['rating'],
            'rating_cleanliness'  => $validated['rating_cleanliness'] ?? null,
            'rating_security'     => $validated['rating_security'] ?? null,
            'rating_facilities'   => $validated['rating_facilities'] ?? null,
            'rating_price'        => $validated['rating_price'] ?? null,
            'rating_friendliness' => $validated['rating_friendliness'] ?? null,
            'comment'             => $validated['comment'],
            'is_anonymous'        => $request->boolean('is_anonymous'),
            'status'              => 'pending',
        ]);

        return redirect()->route('tenant.reviews.index')
            ->with('success', 'Ulasan berhasil dikirim! Menunggu moderasi admin.');
    }

    /**
     * Update ulasan (kalau udah ada).
     */
    public function update(Request $request, Review $review)
    {
        // Cek kepemilikan
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        // Cuma bisa edit kalau status rejected (biar yang approved ga bisa diubah)
        if ($review->status === 'approved') {
            return back()->with('error', 'Ulasan yang sudah disetujui tidak bisa diubah.');
        }

        $validated = $request->validate([
            'rating'              => ['required', 'integer', 'min:1', 'max:5'],
            'rating_cleanliness'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_security'     => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_facilities'   => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_price'        => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_friendliness' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment'             => ['required', 'string', 'min:10', 'max:500'],
            'is_anonymous'        => ['boolean'],
        ]);

        $review->update([
            'rating'              => $validated['rating'],
            'rating_cleanliness'  => $validated['rating_cleanliness'] ?? null,
            'rating_security'     => $validated['rating_security'] ?? null,
            'rating_facilities'   => $validated['rating_facilities'] ?? null,
            'rating_price'        => $validated['rating_price'] ?? null,
            'rating_friendliness' => $validated['rating_friendliness'] ?? null,
            'comment'             => $validated['comment'],
            'is_anonymous'        => $request->boolean('is_anonymous'),
            'status'              => 'pending',
            'approved_at'         => null,
            'approved_by'         => null,
        ]);

        return redirect()->route('tenant.reviews.index')
            ->with('success', 'Ulasan berhasil diperbarui! Menunggu moderasi ulang.');
    }
}