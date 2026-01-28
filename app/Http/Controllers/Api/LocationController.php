<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $locations = Location::orderBy('name')->get();
        } else {
            $locations = $user->locations()->orderBy('name')->get();
        }

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location = Location::create($request->all());

        return response()->json($location, 201);
    }

    public function show($id)
    {
        $location = Location::findOrFail($id);

        // Check access
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->locations->contains($id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($location);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $location = Location::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $location->update($request->all());

        return response()->json($location);
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}
