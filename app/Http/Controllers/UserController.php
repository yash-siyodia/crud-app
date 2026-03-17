<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\UserInviteMail;
use App\Models\User;
use App\Services\ActivityLogService;
use Spatie\Permission\Models\Permission; 
use Spatie\Permission\Models\Role; 
use Illuminate\Database\Eloquent\SoftDeletes;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use SoftDeletes;

    public function __construct()
    {
        $this->middleware('permission:user.view')->only(['index', 'show']);
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.edit')->only(['edit', 'update']);
        $this->middleware('permission:user.delete')->only(['destroy']);
    }
    public function index()
    {
        $users = \App\Models\User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cache roles for 24 hours (roles rarely change)
        $roles = Cache::remember('roles_list', 60 * 60 * 24, function () {
            return Role::all();
        });
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(8);

        // Create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($tempPassword),
        ]);

        $roles = $request->input('roles', []);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        // Log user creation
        ActivityLogService::logCrud('created', 'User', $user, ['roles' => $roles]);

        
        // Send email with password
        \Log::info('MAIL CODE REACHED');
        //Mail::to($user->email)->queue(new UserInviteMail($user, $tempPassword));
        \Log::info('MAIL CODE EXECUTED');
        Mail::to($user->email)->send(new UserInviteMail($user, $tempPassword));
        //try {
        //    Mail::to($user->email)->send(new UserInviteMail($user, $tempPassword));
        //} catch (\Exception $e) {
        //    logger()->error("Mail failed: " . $e->getMessage());
        //}

        return response()->json([
            'message' => 'User created successfully!',
            'redirect' => route('users.index')
        ]);

        //return redirect()->route('users.index')
          //              ->with('success', 'User created & login details sent.');

       
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Cache roles for 24 hours (roles rarely change)
        $roles = Cache::remember('roles_list', 60 * 60 * 24, function () {
            return Role::all();
        });
        // which roles this user has
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $oldData = $user->only(['name', 'email']);
        $user->update($request->only(['name','email']));
        $user->syncRoles($request->roles ?? []);

        // Log user update
        $changes = array_diff_assoc($request->only(['name', 'email']), $oldData);
        if (!empty($changes) || !empty($request->roles)) {
            ActivityLogService::logCrud('updated', 'User', $user, [
                'changes' => $changes,
                'roles' => $request->roles ?? []
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('users.index'),
                'message' => 'User updated successfully'
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated and roles synced.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Log user deletion before deleting
        ActivityLogService::logCrud('deleted', 'User', $user);
        
        $user->delete(); // soft delete

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
