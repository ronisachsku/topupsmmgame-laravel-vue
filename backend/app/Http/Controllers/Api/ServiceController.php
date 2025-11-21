<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_active', true);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with(['durationOther' => function($query) {
                $query->where('is_active', true)->where('stock', '>', 0);
            }, 'sosmed' => function($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    public function categories()
    {
        $categories = Service::select('category')
            ->where('is_active', true)
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
