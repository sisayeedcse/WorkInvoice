<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::latest()->get();

        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'category'      => 'nullable|string|max:100',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', 'Service item added successfully.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'category'      => 'nullable|string|max:100',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Service item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')
            ->with('success', 'Service item deleted.');
    }

    public function search(Request $request)
    {
        $items = Item::where('is_active', true)
            ->where('name', 'like', "%{$request->q}%")
            ->select('id', 'name', 'description', 'default_price', 'unit')
            ->take(10)
            ->get();

        return response()->json($items);
    }
}
