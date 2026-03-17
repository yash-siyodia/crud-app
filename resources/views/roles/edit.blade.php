@extends('layouts.app_with_sidebar')
@section('title','Edit Role')
@section('content')

<div class="w-full p-6">
    <h2 class="text-xl font-bold mb-4">Edit Role</h2>

    <form id="editRoleForm">
        @csrf
        @method('PUT')

        <input type="hidden" id="role_id" value="{{ $role->id }}">

        <div class="mb-3">
            <label class="block font-semibold">Name</label>
            <input type="text" name="name" class="border p-2 w-full" value="{{ old('name', $role->name) }}">
            <span class="text-red-500 text-sm error-name"></span>
        </div>

        <div class="mb-3">
            <label class="block font-semibold">Permissions</label>
            <div>
               @foreach($permissions as $permission)
                    <label class="inline-flex items-center">
                        <input type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                        {{ $permission->name }}
                    </label><br>
                @endforeach
            </div>
            <span class="text-red-500 text-sm error-permissions"></span>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>
</div>

<script>
    $(document).ready(function () {

        // Custom rule: letters only
        $.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters allowed");

        $('#editRoleForm').validate({

            rules: {
                name: {
                    required: true,
                    lettersonly: true
                },
                "permissions[]": {
                    required: true
                }
            },

            messages: {
                name: {
                    required: "Role name is required",
                    lettersonly: "Only letters and spaces allowed"
                },
                "permissions[]": {
                    required: "Select at least one permission"
                }
            },

            errorPlacement: function (error, element) {
                if (element.attr("name") === "permissions[]") {
                    error.appendTo(".error-permissions");
                } else {
                    error.appendTo(element.next());
                }
            },

            submitHandler: function (form) {

                $('.text-red-500').text('');

                let roleId = $('#role_id').val();

                $.ajax({
                    url: "/roles/" + roleId,
                    type: "POST",
                    data: $(form).serialize() + "&_method=PUT",
                    success: function (response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = "{{ route('roles.index') }}";
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('.error-' + key.replace('.', '')).text(value[0]);
                            });
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            }
        });

    });
</script>

@endsection
