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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'personnel_id' => 'nullable|exists:office_personnel,id|unique:users',
            'role' => 'nullable|exists:roles,name',
        ]);

        // Check email uniqueness using blind index
        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($validatedData['email']);
        
        if (User::where('email_search_index', $emailSearchIndex)->exists()) {
            return response()->json([
                'message' => 'The email has already been taken.',
                'errors' => ['email' => ['The email has already been taken.']]
            ], 422);
        }

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
            'email' => 'string|email|max:255',
            'password' => 'nullable|string|min:8',
            'personnel_id' => 'nullable|exists:office_personnel,id|unique:users,personnel_id,' . $id,
            'role' => 'nullable|exists:roles,name',
        ]);

        // Check email uniqueness using blind index (if email is being updated)
        if (isset($validatedData['email'])) {
            $encryptionService = app(\App\Services\EncryptionService::class);
            $emailSearchIndex = $encryptionService->generateBlindIndex($validatedData['email']);
            
            $existingUser = User::where('email_search_index', $emailSearchIndex)
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingUser) {
                return response()->json([
                    'message' => 'The email has already been taken.',
                    'errors' => ['email' => ['The email has already been taken.']]
                ], 422);
            }
        }

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
