<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCollectionController extends Controller
{
    // Rubrik custom action: tampilkan koleksi media milik pengguna.
    public function index(User $user): UserResource
    {
        return new UserResource($user->load('mediaCollection'));
    }

    // Rubrik custom action attach: duplikasi koleksi ditolak dengan status 409.
    public function store(User $user, Media $media): JsonResponse
    {
        if ($user->mediaCollection()->whereKey($media->id)->exists()) {
            return response()->json(['message' => 'Media sudah ada di koleksi pengguna.'], 409);
        }

        $user->mediaCollection()->attach($media->id);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi.',
            'data' => new MediaResource($media),
        ], 201);
    }

    // Rubrik custom action detach: hapus relasi koleksi tanpa menghapus media.
    public function destroy(User $user, Media $media): JsonResponse
    {
        if (! $user->mediaCollection()->detach($media->id)) {
            return response()->json(['message' => 'Media tidak ada di koleksi pengguna.'], 404);
        }

        return response()->json(['message' => 'Media berhasil dihapus dari koleksi.']);
    }
}
