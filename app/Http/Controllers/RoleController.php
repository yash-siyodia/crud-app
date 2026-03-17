<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\ActivityLogService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        // Optional: protect routes with auth middleware
        $this->middleware('auth');
        // Optional: protect with permissions (example)
        // $this->middleware('permission:role.view|role.create|role.edit|role.delete', ['only' => ['index','show']]);
        //$this->middleware('permission:role.view')->only(['index', 'show']);
        //$this->middleware('permission:role.create')->only(['create', 'store']);
        //$this->middleware('permission:role.edit')->only(['edit', 'update']);
        //$this->middleware('permission:role.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        // Cache permissions for 24 hours (permissions rarely change)
        $permissions = Cache::remember('permissions_list', 60 * 60 * 24, function () {
            return Permission::all();
        });
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        // Log role creation
        ActivityLogService::logCrud('created', 'Role', $role, ['permissions' => $request->permissions ?? []]);

        // Clear roles cache when a new role is created
        Cache::forget('roles_list');

        return response()->json([
            'message' => 'Role created successfully',
            'redirect' => route('roles.index')
        ]);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        // Cache permissions for 24 hours (permissions rarely change)
        $permissions = Cache::remember('permissions_list', 60 * 60 * 24, function () {
            return Permission::all();
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'permissions' => 'array'
        ]);

        $role->name = $request->name;
        $role->save();

        $oldPermissions = $role->permissions->pluck('name')->toArray();
        $role->syncPermissions($request->permissions ?? []);

        // Log role update
        ActivityLogService::logCrud('updated', 'Role', $role, [
            'name_changed' => $role->name !== $request->name,
            'permissions_changed' => $oldPermissions !== ($request->permissions ?? [])
        ]);

        // Clear roles cache when a role is updated
        Cache::forget('roles_list');

        // Return JSON response for AJAX requests (consistent with store method)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Role updated successfully',
                'redirect' => route('roles.index')
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id){
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin role cannot be deleted'
            ], 403);
        }

        // Log role deletion
        ActivityLogService::logCrud('deleted', 'Role', $role);

        $role->delete();

        // Clear roles cache when a role is deleted
        Cache::forget('roles_list');

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

}
