@extends('layouts.app_with_sidebar')
@section('title', 'Blogs list')
@section('content')

<div class=" w-full p-6">
    <h2 class="text-xl font-bold mb-4">Blogs</h2>

    <div class="flex justify-between items-center mb-4">
        <!-- add blog button -->
        <a href="{{ route('blogs.create') }}" class="bg-green-500 text-white px-3 py-2 rounded">Add Blog</a>
    </div>

    

    @if(session('success'))
    <p>{{ session('success') }}</p>
    @endif

    <div class="overflow-x-auto w-full mt-4">
        <table id="blogsTable" class="table-auto w-full border display">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="blogTableBody">
                @foreach($blogs as $blog)
                    <tr>
                        <td>{{ $blog->title }}</td>
                        <td>
                            <a href="{{ route('blogs.edit', $blog) }}" class="bg-green-500 text-white px-2 py-1 rounded">Edit</a>
                            <button
                                onclick="deleteBlog({{ $blog->id }}, this)"
                                class="bg-red-600 text-white px-2 py-1 rounded">
                                Delete
                            </button>

                            <a href="{{ route('blogs.pdf', $blog->id) }}"
                            class="bg-red-500 text-white px-2 py-1 rounded">
                            Download PDF
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>
    </div>
    
</div>
<!-- DataTable Initialization -->
<script>
    $(document).ready(function () {
        window.blogTable = $('#blogsTable').DataTable();
    });

    function deleteBlog(blogId, btn) {

        if (!confirm('Are you sure you want to delete this blog?')) {
            return;
        }

        $.ajax({
            url: "/blogs/" + blogId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "DELETE"
            },
            success: function (response) {

                // Remove row from DataTable
                let row = $(btn).closest('tr');
                blogTable.row(row).remove().draw();

                alert('Blog deleted successfully');
            },
            error: function () {
                alert('Something went wrong!');
            }
        });
    }
</script>

@endsection
