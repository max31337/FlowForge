<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserManagementController extends Controller
{
    /**
     * Display a listing of tenant users.
     */
    public function index(): View
    {
        // Only allow users with manage_users permission
        if (!auth()->user()->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        $users = User::where('tenant_id', tenant('id'))
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tenant.users.index', compact('users'));
    }

    /**
     * Show the form for editing a user's role.
     */
    public function editRole(User $user): View
    {
        // Only allow users with manage_users permission
        if (!auth()->user()->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        // Ensure the user belongs to the current tenant
        if ($user->tenant_id !== tenant('id')) {
            abort(404, 'User not found.');
        }

        $roles = Role::where('tenant_id', tenant('id'))->get();

        return view('tenant.users.edit-role', compact('user', 'roles'));
    }

    /**
     * Update a user's role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Only allow users with manage_users permission
        if (!auth()->user()->hasPermission('manage_users')) {
            abort(403, 'You do not have permission to manage users.');
        }

        // Ensure the user belongs to the current tenant
        if ($user->tenant_id !== tenant('id')) {
            abort(404, 'User not found.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Verify the role belongs to the current tenant
        $role = Role::where('id', $request->role_id)
            ->where('tenant_id', tenant('id'))
            ->first();

        if (!$role) {
            abort(422, 'Invalid role selected.');
        }

        $user->update(['role_id' => $role->id]);

        return redirect()->route('tenant.users.index')
            ->with('success', "User role updated successfully!");
    }
}
