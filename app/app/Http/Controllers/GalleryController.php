<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Image;

class GalleryController extends Controller
{
    /**
     * Show the gallery page
     */
    public function index()
    {
        $collections = Collection::orderBy('sort', 'asc')->orderBy('id', 'asc')->get();

        return view('gallery', [
            'collections' => $collections,
            'locationTag' => 'gallery',
        ]);
    }

    /**
     * Show a specific collection
     */
    public function show(Collection $collection)
    {
        $images = Image::where('hidden', false)
            ->where('collection', $collection->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('collection', [
            'collection' => $collection,
            'images' => $images,
            'locationTag' => 'gallery',
        ]);
    }
}
