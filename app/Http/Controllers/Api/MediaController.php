<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    // Rubrik CRUD index: cari judul dan eager load relasi pengguna.
    public function index(Request $request): AnonymousResourceCollection
    {
        $media = Media::query()
            ->with('users:id,name,email')
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->string('search')->toString().'%'))
            ->orderBy('title')
            ->paginate(15);

        return MediaResource::collection($media);
    }

    // Rubrik CRUD show: detail media beserta pengguna yang memasukkannya ke koleksi.
    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users:id,name,email'));
    }

    // Rubrik CRUD store: Form Request memberi respons 422 saat validasi gagal.
    public function store(StoreMediaRequest $request): JsonResponse
    {
        return (new MediaResource(Media::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    // Rubrik CRUD update: PATCH mengubah field yang dikirim saja.
    public function update(UpdateMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media->refresh());
    }

    // Rubrik CRUD destroy: relasi pivot ikut terhapus lewat cascade foreign key.
    public function destroy(Media $media): Response
    {
        $media->delete();

        return response()->noContent();
    }
}
