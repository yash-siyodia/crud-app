<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserImportController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function import(Request $request)
    {
        // Validate only Excel file uploads
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ], [
        'file.required' => 'Please select a file before uploading.',
        'file.mimes' => 'Only Excel files (.xlsx, .xls) are allowed!',
        'file.max' => 'File size must be less than 5MB.',
        ]);

        try {
            // Create instance to capture skipped count
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));

            return back()->with('success', 'Users imported successfully! Skipped ' . $import->skipped . ' duplicate record(s).');

        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}
