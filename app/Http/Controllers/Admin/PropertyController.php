<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyRequest;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::withCount('rooms')->latest()->paginate(10);

        return view('admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('admin.properties.create');
    }

    public function store(PropertyRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('properties', 'public');
        }

        Property::create($data);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Properti berhasil ditambahkan.');
    }

    public function show(Property $property)
    {
        $property->load(['rooms' => fn ($q) => $q->with('activeTenant')->orderBy('room_number', 'asc')]);

        return view('admin.properties.show', compact('property'));
    }

    public function edit(Property $property)
    {
        return view('admin.properties.edit', compact('property'));
    }

    public function update(PropertyRequest $request, Property $property)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($property->photo) {
                Storage::disk('public')->delete($property->photo);
            }
            $data['photo'] = $request->file('photo')->store('properties', 'public');
        } else {
            unset($data['photo']);
        }

        $property->update($data);

        return redirect()->route('admin.properties.index')
            ->with('success', 'Properti berhasil diperbarui.');
    }

    public function destroy(Property $property)
    {
        foreach ($property->rooms as $room) {
            if ($room->photo) {
                Storage::disk('public')->delete($room->photo);
            }
        }
        if ($property->photo) {
            Storage::disk('public')->delete($property->photo);
        }

        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Properti beserta seluruh kamarnya berhasil dihapus.');
    }
}