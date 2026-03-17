@extends('layouts.app_with_sidebar')
@section('title','Create Role')
@section('content')

<div class="w-full p-6">
    <h2 class="text-xl font-bold mb-4">Create Role</h2>

    <form id="createRoleForm">
        @csrf

        <div class="mb-3">
            <label class="block font-semibold">Name</label>
            <input type="text" name="name" class="border p-2 w-full">
            <span class="text-red-500 text-sm error-name"></span>
        </div>

        <div class="mb-3">
            <label class="block font-semibold">Permissions</label>
            @foreach($permissions as $perm)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}">
                    {{ $perm->name }}
                </label><br>
            @endforeach
            <span class="text-red-500 text-sm error-permissions"></span>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>

</div>
<script>
    $(document).ready(function () {

        // Custom rule: letters only
        $.validator.addMethod("lettersonly", function (value, element) {
            return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
        }, "Only letters allowed");

        $('#createRoleForm').validate({

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

                $.ajax({
                    url: "{{ route('roles.store') }}",
                    type: "POST",
                    data: $(form).serialize(),
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

    });
</script>

@endsection
