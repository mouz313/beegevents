<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuSet;
use Illuminate\Http\Request;

class VendorMenuController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->vendorProfile;
        $categories = $profile ? $profile->menuCategories()->with('menuItems')->get() : collect();
        $menuSets = $profile ? $profile->menuSets()->withCount('items')->orderBy('sort_order')->get() : collect();

        return view('vendor.menu.index', compact('profile', 'categories', 'menuSets'));
    }

    public function storeCategory(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        $request->validate(['name' => 'required|string|max:100']);

        $sort = (int) MenuCategory::where('vendor_profile_id', $profile->id)->max('sort_order');
        $category = $profile->menuCategories()->create([
            'name' => $request->name,
            'sort_order' => $sort + 1,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->back()->with('success', 'Category added!');
    }

    public function updateCategory(Request $request, MenuCategory $menuCategory)
    {
        $this->guardOwnership($menuCategory->vendor_profile_id);

        $request->validate(['name' => 'required|string|max:100']);
        $menuCategory->update(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'category' => $menuCategory]);
        }

        return redirect()->back()->with('success', 'Category updated!');
    }

    public function destroyCategory(MenuCategory $menuCategory)
    {
        $this->guardOwnership($menuCategory->vendor_profile_id);
        $menuCategory->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Category deleted!');
    }

    public function storeItem(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = MenuCategory::findOrFail($request->menu_category_id);
        $this->guardOwnership($category->vendor_profile_id);

        $item = $profile->menuItems()->create([
            'menu_category_id' => $request->menu_category_id,
            'name' => $request->name,
            'price' => $request->filled('price') ? $request->price : null,
            'description' => $request->description,
            'is_available' => $request->boolean('is_available', true),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'item' => $item->load('menuCategory')]);
        }

        return redirect()->back()->with('success', 'Menu item added!');
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        $this->guardOwnership($menuItem->vendor_profile_id);

        $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $menuItem->update([
            'menu_category_id' => $request->menu_category_id,
            'name' => $request->name,
            'price' => $request->filled('price') ? $request->price : null,
            'description' => $request->description,
            'is_available' => $request->boolean('is_available', true),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'item' => $menuItem->load('menuCategory')]);
        }

        return redirect()->back()->with('success', 'Menu item updated!');
    }

    public function destroyItem(MenuItem $menuItem)
    {
        $this->guardOwnership($menuItem->vendor_profile_id);
        $menuItem->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Menu item deleted!');
    }

    public function partial(Request $request)
    {
        $request->validate(['category_id' => 'required|exists:menu_categories,id']);

        $category = MenuCategory::with('menuItems')->findOrFail($request->category_id);
        $this->guardOwnership($category->vendor_profile_id);

        return view('vendor.menu.items', compact('category'));
    }

    public function storeSet(Request $request)
    {
        $profile = auth()->user()->vendorProfile;
        if (!$profile) {
            return redirect()->route('vendor.profile.create');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:menu_items,id',
        ]);

        $sort = (int) MenuSet::where('vendor_profile_id', $profile->id)->max('sort_order');
        $set = $profile->menuSets()->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $sort + 1,
        ]);

        $this->syncSetItems($profile, $set, $request->input('item_ids', []));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'set' => $set->load('items')]);
        }

        return redirect()->back()->with('success', 'Menu set created!');
    }

    public function updateSet(Request $request, MenuSet $menuSet)
    {
        $this->guardOwnership($menuSet->vendor_profile_id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:menu_items,id',
        ]);

        $menuSet->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncSetItems($menuSet->vendorProfile, $menuSet, $request->input('item_ids', []));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'set' => $menuSet->load('items')]);
        }

        return redirect()->back()->with('success', 'Menu set updated!');
    }

    public function destroySet(MenuSet $menuSet)
    {
        $this->guardOwnership($menuSet->vendor_profile_id);
        $menuSet->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Menu set deleted!');
    }

    public function setPartial(Request $request)
    {
        $request->validate(['set_id' => 'required|exists:menu_sets,id']);

        $set = MenuSet::with('items')->findOrFail($request->set_id);
        $this->guardOwnership($set->vendor_profile_id);

        $items = $set->vendorProfile->menuItems()->with('menuCategory')->orderBy('menu_category_id')->get();

        return view('vendor.menu.sets', compact('set', 'items'));
    }

    private function syncSetItems($profile, MenuSet $set, array $itemIds): void
    {
        $validIds = MenuItem::where('vendor_profile_id', $profile->id)
            ->whereIn('id', $itemIds)
            ->pluck('id');

        $set->items()->sync($validIds);
    }

    private function guardOwnership(int $vendorProfileId): void
    {
        abort_unless(auth()->user()->vendorProfile?->id === $vendorProfileId, 403);
    }
}
