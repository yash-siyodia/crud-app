<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->get();
        return view('blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('blogs.create');
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        // Log blog creation
        ActivityLogService::logCrud('created', 'Blog', $blog);

        return response()->json([
            'message' => 'Blog created successfully',
            'redirect' => route('blogs.index')
        ]);
    }

    public function edit(Blog $blog)
    {
        return view('blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog){
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
        ]);

        $oldData = $blog->only(['title', 'content']);
        $blog->update($request->only('title', 'content'));

        // Log blog update
        $changes = array_diff_assoc($request->only('title', 'content'), $oldData);
        if (!empty($changes)) {
            ActivityLogService::logCrud('updated', 'Blog', $blog, ['changes' => $changes]);
        }

        return response()->json([
            'message' => 'Blog updated successfully',
            'redirect' => route('blogs.index')
        ]);
    }

    public function destroy(Blog $blog){
        // Log blog deletion
        ActivityLogService::logCrud('deleted', 'Blog', $blog);
        
        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully'
        ]);
    }

    public function downloadPdf($id)
    {
        $blog = Blog::findOrFail($id);
        $pdf = Pdf::loadView('blogs.pdf', compact('blog'));
        return $pdf->download('blog-'.$blog->id.'.pdf');
    }
}
