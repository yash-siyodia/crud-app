@extends('layouts.app_with_sidebar')
@section('title', 'Add blogs')

@section('content')

    <div class="max-w-3xl mx-auto bg-white p-6 shadow-md rounded">

        <h2 class="text-xl font-bold mb-4">Create Blog</h2>

        <form id="createBlogForm">
            @csrf

            <div class="mb-3">
                <input type="text" name="title" placeholder="Title"
                    class="border p-2 w-full">
                <span class="text-red-500 text-sm error-title"></span>
            </div>

            <div class="mb-3">
                <textarea name="content" placeholder="Content"
                    class="border p-2 w-full"></textarea>
                <span class="text-red-500 text-sm error-content"></span>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </form>

    </div>
    <script>
        $(document).ready(function () {

            $('#createBlogForm').validate({

                rules: {
                    title: {
                        required: true
                    },
                    content: {
                        required: true
                    }
                },

                messages: {
                    title: {
                        required: "Title is required"
                    },
                    content: {
                        required: "Content is required"
                    }
                },

                errorPlacement: function (error, element) {
                    error.appendTo(element.next());
                },

                submitHandler: function (form) {

                    $('.text-red-500').text('');

                    $.ajax({
                        url: "{{ route('blogs.store') }}",
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
