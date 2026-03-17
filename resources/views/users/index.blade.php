@extends('layouts.app_with_sidebar')

@section('title', 'User List')

@section('content')


    <div class=" w-full p-6 ">

        <h2 class="text-xl font-bold mb-4">User List</h2>

        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-2 mt-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('file'))
        <div class="bg-red-200 text-red-800 p-2 rounded mt-2">
            {{ $errors->first('file') }}
        </div>
        @endif

        @if(session('error'))
            <div class="bg-red-200 text-red-800 p-2 mt-2 rounded">
                {{ session('error') }}
            </div>
        @endif


        <!-- Import Form -->
        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="flex space-x-3 mb-4">
            @csrf
            <input type="file" name="file" id="fileInput" class="border p-2 rounded" required>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Import Users
            </button>
        </form>
        <span id="fileError" class="text-red-500 text-sm"></span>

        <!-- add user -->
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('users.create') }}"
            class="bg-green-500 text-white px-3 py-2 rounded">
                Add New User
            </a>
        </div>


        <!-- Users Table -->
        <table id="usersTable" class="min-w-full w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 border text-left">ID</th>
                    <th class="px-4 py-2 border text-left">Name</th>
                    <th class="px-4 py-2 border text-left">Email</th>
                    <th class="px-4 py-2 border text-left">Roles</th>
                    <th class="px-4 py-2 border text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                    <tr class="odd:bg-white even:bg-gray-100">
                        <td class="px-4 py-2 border">{{ $user->id }}</td>
                        <td class="px-4 py-2 border">{{ $user->name }}</td>
                        <td class="px-4 py-2 border">{{ $user->email }}</td>

                        <td class="px-4 py-2 border">
                            @php $roles = $user->getRoleNames(); @endphp
                            @if($roles->isEmpty())
                                <span class="text-sm text-gray-500">—</span>
                            @else
                                @foreach($roles as $role)
                                    <span class="inline-block text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 mr-1">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            @endif
                        </td>

                        <td class="px-4 py-2 border">
                            <div class="flex items-center space-x-2">
                                @can('user.edit')
                                    <a href="{{ route('users.edit', $user->id) }}" class="bg-yellow-400 px-2 py-1 rounded text-black">Edit</a>
                                @endcan

                                <button onclick="deleteuser({{ $user->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <!-- Pagination (if you pass paginator) -->
    @if(method_exists($users, 'links'))
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable();
        });

        function deleteuser(id){
            if(confirm("Are you sure to delete?")){

                $.ajax({
                    url: "/users/" + id,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(){
                        alert("Deleted Successfully");
                        location.reload();
                    }
                });

            }
        }
        $("form").on("submit", function (e) {

            let fileInput = $("#fileInput");
            let filePath = fileInput.val();
            let allowedExtensions = /(\.xlsx|\.xls)$/i;

            $("#fileError").text("");  // Reset message

            if (!allowedExtensions.exec(filePath)) {
                $("#fileError").text("Only Excel files (.xlsx .xls) are allowed!");
                fileInput.val("");
                e.preventDefault();
            }
        });

    </script>
@endsection
