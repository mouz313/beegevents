<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\HallUnit;
use App\Models\ServiceListing;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('customer.cart', compact('cart'));
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hall_unit,service_listing',
            'id' => 'required|integer',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $cart = session()->get('cart', []);
        $cart = $this->resolveItemIntoCart($cart, $request->type, (int) $request->id, $request->date);
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart), 'cart' => $cart]);
        }

        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function addBundle(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:hall_unit,service_listing',
            'items.*.id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);

        foreach ($request->items as $item) {
            $cart = $this->resolveItemIntoCart($cart, $item['type'], (int) $item['id'], $request->date);
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart), 'cart' => $cart]);
        }

        return redirect()->route('customer.cart')->with('success', 'Bundle added to cart!');
    }

    protected function resolveItemIntoCart(array $cart, string $type, int $id, string $date): array
    {
        if ($type === 'hall_unit') {
            $item = HallUnit::with('hall')->findOrFail($id);
            $key = 'hall_unit_'.$item->id.'_'.$date;
            $cart[$key] = [
                'type' => 'hall_unit',
                'id' => $item->id,
                'date' => $date,
                'name' => $item->hall->name.' - '.$item->unit_name,
                'price' => $item->base_price,
                'capacity' => $item->min_capacity.'-'.$item->max_capacity,
                'vendor_id' => $item->hall->vendor_profile_id,
            ];
        } else {
            $item = ServiceListing::with('serviceCategory')->findOrFail($id);
            $key = 'listing_'.$item->id.'_'.$date;
            $cart[$key] = [
                'type' => 'service_listing',
                'id' => $item->id,
                'date' => $date,
                'name' => $item->title,
                'price' => $item->price,
                'price_unit' => $item->price_unit,
                'category' => $item->serviceCategory->name ?? '',
                'vendor_id' => $item->vendor_profile_id,
            ];
        }

        return $cart;
    }

    public function removeItem(Request $request, $key)
    {
        $cart = session()->get('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => count($cart)]);
        }

        return redirect()->route('customer.cart')->with('success', 'Item removed!');
    }

    public function clear()
    {
        session()->forget('cart');
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('customer.cart')->with('success', 'Cart cleared!');
    }
}
