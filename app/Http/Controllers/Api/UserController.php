<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('locations')->get();

        return response()->json($users->map(function ($user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
                'location_ids' => $user->locations->pluck('id')->toArray(),
                'created_at' => $user->created_at,
            ];
        }));
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'role' => 'required|in:admin,staff,radnik',
            ]);

            $user = User::findOrFail($id);
            $user->role = $request->role;
            $user->save();

            return response()->json([
                'message' => 'Role updated successfully',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to update role',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['first_name', 'last_name', 'email']));

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    public function assignLocation(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        $user = User::findOrFail($id);

        // Check if already assigned
        if (!$user->locations()->where('location_id', $request->location_id)->exists()) {
            $user->locations()->attach($request->location_id);
        }

        return response()->json([
            'message' => 'Location assigned successfully',
        ]);
    }

    public function removeLocation(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|uuid|exists:locations,id',
        ]);

        $user = User::findOrFail($id);
        $user->locations()->detach($request->location_id);

        return response()->json([
            'message' => 'Location removed successfully',
        ]);
    }
}
