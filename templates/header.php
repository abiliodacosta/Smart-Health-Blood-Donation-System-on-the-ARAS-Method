<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $page_title ?? 'Smart-Health'; ?></title>
    <?php
    $is_root_h = (isset($current_page) && ($current_page == 'index' || $current_page == 'dashboard'));
    $base_h = $is_root_h ? '' : '../';
    ?>
    <link rel="icon" type="image/png" href="<?php echo $base_h; ?>assets/images/favicon.png">
    
    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <!-- DataTables Buttons CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .sidebar {
            position: sticky !important;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            width: 16rem;
            transition: all 0.3s ease;
        }
        .sidebar.toggled {
            width: 6.5rem !important;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed !important;
                display: flex !important;
                flex-direction: column;
                top: 0;
                left: -16rem; /* hidden off-screen */
                width: 16rem !important;
                height: 100vh;
                z-index: 9999;
                transition: left 0.3s ease;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
                visibility: visible !important;
            }
            body.sidebar-toggled .sidebar,
            .sidebar.toggled {
                left: 0 !important;
                width: 16rem !important;
            }
            #content-wrapper {
                width: 100%;
            }
        }
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }
    </style>

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?php echo $base_h; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .bg-gradient-primary { background-color: #4f46e5; background-image: linear-gradient(180deg, #4f46e5 10%, #4338ca 100%); }
        .sidebar-dark .nav-item .nav-link i { color: rgba(255, 255, 255, 0.8); }
        .card { border-radius: 1rem; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .card-header { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; border-radius: 1rem 1rem 0 0 !important; }
        .badge-ranking { background: #4f46e5; color: white; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.8rem; }

        /* Collapsible Sidebar +/- Toggle */
        .collapse-arrow {
            transition: transform 0.3s ease;
            font-size: 0.75rem;
            opacity: 0.8;
            flex-shrink: 0;
        }
        /* Switch to minus when open */
        .nav-link[aria-expanded="true"] .collapse-arrow::before {
            content: "\f068"; /* fa-minus */
        }
        .nav-link[aria-expanded="false"] .collapse-arrow::before,
        .nav-link.collapsed .collapse-arrow::before {
            content: "\f067"; /* fa-plus */
        }
        /* Collapse inner items - FIX OVERFLOW */
        .collapse-inner {
            border-radius: 0.5rem !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58,59,69,.15) !important;
            margin: 0 0.75rem 0.75rem 0.75rem;
            overflow: hidden;
            width: calc(100% - 1.5rem);
        }
        .collapse-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            text-align: left;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            color: #3a3b45;
            border-radius: 0.35rem;
            transition: all 0.2s;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
            gap: 10px;
        }
        .collapse-item:hover {
            background-color: #eaecf4;
            color: #4f46e5;
            text-decoration: none;
            padding-left: 1.25rem;
        }
        .collapse-item.active {
            font-weight: 700;
            color: #4f46e5;
            background-color: #eaecf4;
        }
        .nav-item .nav-link {
            transition: all 0.2s ease;
        }
        .nav-item .nav-link:hover {
            padding-left: 1.25rem !important;
            background: rgba(255,255,255,0.05);
        }
        /* Sidebar nav-link text truncation fix */
        .sidebar .nav-link {
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar.toggled .nav-link {
            white-space: nowrap;
        }
        /* Sidebar parent link flex layout */
        .sidebar .nav-link.d-flex {
            align-items: center;
        }
        .sidebar .nav-link.d-flex > span:first-child {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Responsive sidebar width */
        @media (max-width: 768px) {
            .sidebar {
                width: 6.5rem !important;
            }
            .collapse-inner {
                margin: 0 0.25rem 0.5rem 0.25rem;
                width: calc(100% - 0.5rem);
            }
            .collapse-item {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }
        }
        /* Shrink sidebar parent links */
        #accordionSidebar .nav-item .nav-link span {
            font-size: 0.88rem;
        }

        /* PODIUM IMPROVEMENTS */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 20px;
            margin: 3rem 0 5rem;
            padding: 0 15px;
        }

        .podium-item {
            background: #ffffff !important;
            border-radius: 2rem;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease;
            position: relative;
            flex: 1;
            max-width: 280px;
            border: 1px solid #e2e8f0 !important;
        }

        .p-rank-1 { order: 2; min-height: 320px; border-bottom: 8px solid #fbbf24 !important; z-index: 3; }
        .p-rank-2 { order: 1; min-height: 280px; border-bottom: 8px solid #94a3b8 !important; z-index: 2; }
        .p-rank-3 { order: 3; min-height: 250px; border-bottom: 8px solid #b45309 !important; z-index: 1; }

        .podium-medal {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            z-index: 10;
        }

        .p-rank-1 .podium-medal { color: #fbbf24; border: 4px solid #fbbf24; }
        .p-rank-2 .podium-medal { color: #94a3b8; border: 4px solid #94a3b8; }
        .p-rank-3 .podium-medal { color: #b45309; border: 4px solid #b45309; }

        .podium-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 1rem auto;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 4px solid #f1f5f9;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .p-rank-1 .podium-img { width: 110px; height: 110px; font-size: 3.5rem; color: #fbbf24; background: #fffbeb; }
        .p-rank-2 .podium-img { color: #94a3b8; background: #f1f5f9; }
        .p-rank-3 .podium-img { color: #b45309; background: #fff7ed; }

        .podium-name {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 1.1rem;
            line-height: 1.2;
        }

        .podium-score {
            font-weight: 900;
            color: #4f46e5;
            font-size: 1.6rem;
            margin: 0.5rem 0;
        }

        @media (max-width: 768px) {
            .podium-container {
                flex-direction: column;
                align-items: center;
                gap: 40px;
                margin-bottom: 2rem;
            }
            .podium-item {
                width: 100%;
                max-width: 100%;
                min-height: auto !important;
                order: unset !important;
                padding: 2.5rem 1.5rem 1.5rem;
            }
            .p-rank-1 { order: 1 !important; }
            .p-rank-2 { order: 2 !important; }
            .p-rank-3 { order: 3 !important; }
        }
    </style>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
