<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TourPackage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::all();
        return response()->json($packages, 200);
    }

    public function show($slug)
    {

        $package = TourPackage::where('slug', $slug)->first();

        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        return response()->json($package, 200);
        }
}
