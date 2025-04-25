<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function upload(Request $request)
    {
        $urls = [];
        
        foreach ($request->file('files') as $file) {

            $path = $file->store('assets', 'public');
            $urls[] = ['src' => asset('storage/' . $path)];
        }

        return response()->json($urls);
    }

    public function delete(Request $request)
    {
        $assets = $request->all(); // ex: [ { src: "url" }, ... ]
        foreach ($assets as $asset) {
            $path = str_replace(asset('storage/') . '/', '', $asset['src']);
            Storage::disk('public')->delete($path);
        }

        return response()->json(['message' => 'Deleted']);
    }
}