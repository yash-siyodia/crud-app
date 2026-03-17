@extends('layouts.app_with_sidebar')
@section('title', 'Edit blogs')

@section('content')
    <h2>Edit Blog</h2>

    <form id="editBlogForm">
        @csrf
        @method('PUT')

        <input type="hidden" id="blog_id" value="{{ $blog->id }}">

        <div class="mb-3">
            <input type="text" name="title"
                value="{{ $blog->title }}"
                class="border p-2 w-full">
            <span class="text-red-500 text-sm error-title"></span>
        </div>

        <div class="mb-3">
            <textarea name="content"
                    class="border p-2 w-full">{{ $blog->content }}</textarea>
            <span class="text-red-500 text-sm error-content"></span>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>
    <script>
        $(document).ready(function () {

            $('#editBlogForm').validate({

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

                    let blogId = $('#blog_id').val();

                    $.ajax({
                        url: "/blogs/" + blogId,
                        type: "POST",
                        data: $(form).serialize() + "&_method=PUT",
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
