<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_categories,slug',
            'description' => 'nullable|string',
        ]);

        $category = ServiceCategory::create($request->all());

        if ($request->ajax()) return response()->json(['success' => true, 'category' => $category]);
        return redirect()->back()->with('success', 'Category created!');
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_categories,slug,' . $serviceCategory->id,
            'description' => 'nullable|string',
        ]);

        $serviceCategory->update($request->all());

        if ($request->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Category updated!');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->back()->with('success', 'Category deleted!');
    }
}
