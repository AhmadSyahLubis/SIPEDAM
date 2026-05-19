<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::orderBy('name');
        
        if ($request->has('type') && in_array($request->type, ['laporan', 'layanan'])) {
            $query->where('type', $request->type);
        }
        
        // For public/users we should only show active categories.
        // Admin gets everything automatically or we can enforce ?all=true
        if (!$request->has('all')) {
            $query->where('is_active', true);
        }
        
        $categories = $query->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:laporan,layanan',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:laporan,layanan',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        // Prevent deletion if there are attached reports or services
        $hasReports = \App\Models\Report::where('category_id', $id)->exists();
        $hasServices = \App\Models\Service::where('category_id', $id)->exists();

        if ($hasReports || $hasServices) {
            return response()->json([
                'success' => false, 
                'message' => 'Kategori tidak dapat dihapus karena sudah dipakai oleh Laporan/Layanan. Silakan nonaktifkan saja.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }

    public function toggleActive($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Status kategori berhasil diubah',
            'is_active' => $category->is_active
        ]);
    }
}
