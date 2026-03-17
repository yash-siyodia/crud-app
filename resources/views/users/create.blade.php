@extends('layouts.app_with_sidebar')

@section('title', 'Add User')

@section('content')

    <div class="bg-white p-6 shadow rounded max-w-lg mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <h2 class="text-xl font-bold mb-4">Create User</h2>

        @if(session('success'))
            <div class="bg-green-200 p-2 mb-3">{{ session('success') }}</div>
        @endif

        <form id="createUserForm">
            @csrf

            <div class="mb-3">
                <label class="block font-medium">Name</label>
                <input type="text" name="name" class="border p-2 w-full" required>
            </div>

            <div class="mb-3">
                <label class="block font-medium">Email</label>
                <input type="email" name="email" class="border p-2 w-full" required>
            </div>

            <div class="mb-3">
                <label class="block font-semibold mb-2">Roles</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                @if(in_array($role->name, old('roles', $userRoles ?? []))) checked @endif
                                class="mr-2"
                            >
                            <span class="px-2 py-1 bg-gray-100 rounded">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
            </div>

            <button class="bg-blue-600 text-black px-4 py-2 rounded">
                Save & Send Invite
            </button>

        </form>

    </div>

<script>
    $(document).ready(function () {

        // Custom rule: letters only
        $.validator.addMethod("lettersOnly", function (value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters are allowed");

        $("#createUserForm").validate({

            rules: {
                name: {
                    required: true,
                    lettersOnly: true
                },
                email: {
                    required: true,
                    email: true
                },
                "roles[]": {
                    required: true
                }
            },

            messages: {
                name: {
                    required: "Name is required",
                    lettersOnly: "Name must contain only letters"
                },
                email: {
                    required: "Email is required",
                    email: "Enter a valid email address"
                },
                "roles[]": {
                    required: "Please select at least one role"
                }
            },

            errorElement: "div",
            errorClass: "text-red-500 text-sm mt-1",

            submitHandler: function (form) {

                $('.text-red-500').text(''); // clear server errors

                $.ajax({
                    url: "{{ route('users.store') }}",
                    type: "POST",
                    data: $(form).serialize(),

                    success: function (response) {
                        window.location.href = response.redirect;
                    },

                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('.error-' + key).text(value[0]);
                            });
                        }
                    }
                });
            }
        });

    });
</script>



@endsection
