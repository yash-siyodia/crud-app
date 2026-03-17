@extends('layouts.app_with_sidebar')

@section('title','Edit User')

@section('content')
<div class="w-full p-6 max-w-2xl mx-auto bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Edit User</h2>

    <form id="editUserForm">
        @csrf
        @method('PUT')

        <input type="hidden" id="user_id" value="{{ $user->id }}">

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name"
                value="{{ old('name', $user->name) }}"
                class="border p-2 w-full">
            <span class="text-red-500 text-sm error-name"></span>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email"
                value="{{ old('email', $user->email) }}"
                class="border p-2 w-full">
            <span class="text-red-500 text-sm error-email"></span>
        </div>

        <div class="mb-3">
            <label>Roles</label><br>
            @foreach($roles as $role)
                <label>
                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                        @if(in_array($role->name, $userRoles)) checked @endif>
                    {{ $role->name }}
                </label><br>
            @endforeach
            <span class="text-red-500 text-sm error-roles"></span>
        </div>

        <button class="bg-blue-500 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>

</div>

<script>
    $(document).ready(function () {

        $('#editUserForm').validate({
            rules: {
                name: {
                    required: true,
                    lettersonly: true
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
                    lettersonly: "Only letters and spaces allowed"
                },
                email: {
                    required: "Email is required",
                    email: "Enter a valid email address"
                },
                "roles[]": {
                    required: "Please select at least one role"
                }
            },

            errorPlacement: function (error, element) {
                if (element.attr("name") === "roles[]") {
                    error.appendTo(".error-roles");
                } else {
                    error.appendTo(element.next());
                }
            },

            submitHandler: function (form) {

                $('.text-red-500').text('');

                let userId = $('#user_id').val();

                $.ajax({
                    url: "/users/" + userId,
                    type: "POST",
                    data: $(form).serialize() + "&_method=PUT",
                    success: function (response) {
                        window.location.href = response.redirect;
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('.error-' + key.replace('.', '')).text(value[0]);
                            });
                        }
                    }
                });
            }
        });

        /* Custom rule for letters only */
        $.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        });

    });
</script>


@endsection
