<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Portal ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css')
</head>
<body data-sidebar="dark" class="erp-body" id="erp-body">

    <!-- Begin page -->
    <div id="layout-wrapper">

        {{-- Top Navigation Bar --}}
        @include('layouts.partials.navbar')

        {{-- Left Vertical Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Mobile Overlay Backdrop --}}
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    {{-- Page Title & Breadcrumb header --}}
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18 text-uppercase page-title-heading">
                            @yield('page-title', 'DASHBOARD')
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>

                    {{-- Main Page Body Content --}}
                    @yield('content')

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    @stack('scripts')
</body>
</html>
