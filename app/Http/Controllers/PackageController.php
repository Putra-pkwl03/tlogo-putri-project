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

    public function show($id)
    {
        $package = TourPackage::find($id);
        return response()->json($package, 200);
    }
}
