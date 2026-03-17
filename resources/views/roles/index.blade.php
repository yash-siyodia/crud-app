@extends('layouts.app_with_sidebar')
@section('title','Roles')
@section('content')

<div class="w-full p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Roles</h2>
        <a href="{{ route('roles.create') }}" class="bg-green-500 text-white px-3 py-2 rounded">Add Role</a>
    </div>

    @if(session('success'))
        <div class="bg-green-200 p-2 text-green-800 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table id="rolesTable" class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Name</th>
                <th class="border px-4 py-2">Permissions</th>
                <th class="border px-4 py-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td class="border px-4 py-2">{{ $role->id }}</td>
                <td class="border px-4 py-2">{{ $role->name }}</td>
                <td class="border px-4 py-2">
                    @foreach($role->permissions as $p)
                        <span class="inline-block bg-gray-200 px-2 py-1 rounded mr-1 text-sm">{{ $p->name }}</span>
                    @endforeach
                </td>
                <td class="border px-4 py-2">
                    <a href="{{ route('roles.edit', $role->id) }}" class="bg-yellow-400 px-2 py-1 rounded">Edit</a>

                    <button
                        onclick="deleteRole({{ $role->id }})"
                        class="bg-red-500 text-white px-2 py-1 rounded">
                        Delete
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
<script>

    $(document).ready(function () {
            $('#rolesTable').DataTable();
    });

    function deleteRole(roleId) {

        if (!confirm('Delete role?')) {
            return;
        }

        $.ajax({
            url: "/roles/" + roleId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "DELETE"
            },
            success: function (response) {
                alert(response.message);
                location.reload(); // refresh list
            },
            error: function (xhr) {
                alert('Something went wrong!');
            }
        });
    }
</script>

@endsection
