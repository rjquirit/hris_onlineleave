<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'personnel'])->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'personnel_id' => 'nullable|exists:office_personnel,id|unique:users',
            'role' => 'nullable|exists:roles,name',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        
        $user = User::create($validatedData);

        if (isset($validatedData['role'])) {
            $user->assignRole($validatedData['role']);
        }

        return response()->json($user->load(['roles', 'personnel']), 201);
    }

    public function show($id)
    {
        return User::with(['roles', 'personnel'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'personnel_id' => 'nullable|exists:office_personnel,id|unique:users,personnel_id,' . $id,
            'role' => 'nullable|exists:roles,name',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        if (isset($validatedData['role'])) {
            $user->syncRoles([$validatedData['role']]);
        }

        return response()->json($user->load(['roles', 'personnel']));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        return response()->json(['message' => 'Role assigned successfully', 'user' => $user->load('roles')]);
    }
}
