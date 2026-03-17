<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <!-- jQuery (DataTables depends on jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!-- jQuery message show  -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <!-- jQuery sweet alert  -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS (single include) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    {{-- If you still use a compiled app.css (e.g. with Tailwind compiled into it) keep this.
       If your app.css already includes Tailwind or Bootstrap via npm, you can omit the CDN above
       and let Vite handle everything. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

    <div class="d-flex">

        <!-- Sidebar (fixed width) -->
        <aside class="bg-dark text-white p-4" style="width:16rem; min-height:100vh; position:sticky; top:0;">
            @include('layouts.sidebar')
        </aside>

        <!-- Main content (take remaining width) -->
        <main class="flex-grow-1" style="min-height:100vh;">
            <!-- Navigation bar -->
            <div class="bg-white shadow sticky-top" style="z-index:50;">
                @include('layouts.navigation')
            </div>

            <!-- Page content -->
            <div class="container-fluid p-4">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts') 

    {{-- If you use Vite and app.js contains scripts, it is safe to keep @vite above or call it here:
         If you prefer scripts at the end: @vite(['resources/css/app.css', 'resources/js/app.js']) here instead of head --}}

        
</body>
</html>
