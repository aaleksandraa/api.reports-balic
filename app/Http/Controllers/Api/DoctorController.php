<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('locations')->orderBy('first_name')->get();

        return response()->json($doctors->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'first_name' => $doctor->first_name,
                'last_name' => $doctor->last_name,
                'initials' => $doctor->initials,
                'email' => $doctor->email,
                'role' => $doctor->role,
                'active' => $doctor->active,
                'location_ids' => $doctor->locations->pluck('id')->toArray(),
                'created_at' => $doctor->created_at,
                'updated_at' => $doctor->updated_at,
            ];
        }));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'initials' => 'required|string|max:10',
            'email' => 'nullable|email',
            'role' => 'required|in:doctor,associate,staff',
            'active' => 'boolean',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::create($request->except('location_ids'));

        if ($request->has('location_ids')) {
            $doctor->locations()->sync($request->location_ids);
        }

        $doctor->load('locations');

        return response()->json([
            'id' => $doctor->id,
            'first_name' => $doctor->first_name,
            'last_name' => $doctor->last_name,
            'initials' => $doctor->initials,
            'email' => $doctor->email,
            'role' => $doctor->role,
            'active' => $doctor->active,
            'location_ids' => $doctor->locations->pluck('id')->toArray(),
            'created_at' => $doctor->created_at,
            'updated_at' => $doctor->updated_at,
        ], 201);
    }

    public function show($id)
    {
        $doctor = Doctor::with('locations')->findOrFail($id);

        return response()->json([
            'id' => $doctor->id,
            'first_name' => $doctor->first_name,
            'last_name' => $doctor->last_name,
            'initials' => $doctor->initials,
            'email' => $doctor->email,
            'role' => $doctor->role,
            'active' => $doctor->active,
            'location_ids' => $doctor->locations->pluck('id')->toArray(),
            'created_at' => $doctor->created_at,
            'updated_at' => $doctor->updated_at,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctor = Doctor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'initials' => 'string|max:10',
            'email' => 'nullable|email',
            'role' => 'in:doctor,associate,staff',
            'active' => 'boolean',
            'location_ids' => 'array',
            'location_ids.*' => 'exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor->update($request->except('location_ids'));

        if ($request->has('location_ids')) {
            $doctor->locations()->sync($request->location_ids);
        }

        $doctor->load('locations');

        return response()->json([
            'id' => $doctor->id,
            'first_name' => $doctor->first_name,
            'last_name' => $doctor->last_name,
            'initials' => $doctor->initials,
            'email' => $doctor->email,
            'role' => $doctor->role,
            'active' => $doctor->active,
            'location_ids' => $doctor->locations->pluck('id')->toArray(),
            'created_at' => $doctor->created_at,
            'updated_at' => $doctor->updated_at,
        ]);
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return response()->json(['message' => 'Doctor deleted successfully']);
    }

    public function byLocation($locationId)
    {
        $doctors = Doctor::whereHas('locations', function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        })->where('active', true)->orderBy('first_name')->get();

        return response()->json($doctors);
    }

    public function assignLocation(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::findOrFail($id);

        // Check if already assigned
        if (!$doctor->locations()->where('location_id', $request->location_id)->exists()) {
            $doctor->locations()->attach($request->location_id);
        }

        return response()->json([
            'message' => 'Location assigned successfully',
        ]);
    }

    public function removeLocation(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::findOrFail($id);
        $doctor->locations()->detach($request->location_id);

        return response()->json([
            'message' => 'Location removed successfully',
        ]);
    }
}
