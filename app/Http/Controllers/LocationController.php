<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::paginate(15);
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone' => 'required|string|max:255',
            'shelf' => 'required|string|max:255',
            'slot' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'เพิ่มตำแหน่งเก็บสินค้าสำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $batches = $location->batches()->with('product')->paginate(10);
        return view('locations.show', compact('location', 'batches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'zone' => 'required|string|max:255',
            'shelf' => 'required|string|max:255',
            'slot' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'อัปเดตตำแหน่งเก็บสินค้าสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'ลบตำแหน่งเก็บสินค้าสำเร็จ');
    }
}
