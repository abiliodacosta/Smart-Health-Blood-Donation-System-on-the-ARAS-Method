<?php
$is_root = (isset($current_page) && ($current_page == 'index' || $current_page == 'dashboard'));
$base = $is_root ? '' : '../';
$to_pages = $is_root ? 'pages/' : '';
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $base; ?>index">
        <div class="sidebar-brand-icon">
            <span class="fa-stack fa-1x">
                <i class="fas fa-globe fa-stack-1x text-white-50"></i>
                <i class="fas fa-droplet fa-stack-1x text-white" style="font-size: 0.55em; margin-top: 1px;"></i>
            </span>
        </div>
        <div class="sidebar-brand-text mx-2" style="font-size: 0.85rem; white-space: nowrap; letter-spacing: 1px;">SMART-HEALTH</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo $current_page == 'index' ? 'active' : ''; ?>">
        <a class="nav-link" href="<?php echo $base; ?>index">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <?php if ($_SESSION['level'] == 'Administrator'): ?>
        <!-- Collapsible: Pages Utilizador -->
        <?php
        $user_pages = ['users'];
        $user_open = in_array($current_page, $user_pages);
        ?>
        <li class="nav-item">
            <a class="nav-link collapsed d-flex justify-content-between align-items-center"
                href="#collapseUsers" data-toggle="collapse" aria-expanded="<?php echo $user_open ? 'true' : 'false'; ?>">
                <span class="d-flex align-items-center">
                    <span class="fa-stack fa-1x mr-1" style="font-size: 0.85rem; width: 1.5rem;">
                        <i class="fas fa-globe fa-stack-1x text-white-50"></i>
                        <i class="fas fa-droplet fa-stack-1x text-white" style="font-size: 0.5rem; margin-top: 1px;"></i>
                    </span>
                    <span>Pages Admin</span>
                </span>
            </a>
            <div class="collapse <?php echo $user_open ? 'show' : ''; ?>" id="collapseUsers">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo $current_page == 'users' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>users">
                        <i class="fas fa-user-shield fa-fw mr-1"></i> Utilizadór</a>
                </div>
            </div>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">
    <?php endif; ?>

    <!-- Collapsible: Dadus Sistema -->
    <?php
    $dadus_pages = ['alternatives', 'criteria', 'evaluations', 'cripsi'];
    $dadus_open = in_array($current_page, $dadus_pages);
    ?>
    <li class="nav-item">
        <a class="nav-link collapsed d-flex justify-content-between align-items-center"
            href="#collapseDadus" data-toggle="collapse" aria-expanded="<?php echo $dadus_open ? 'true' : 'false'; ?>">
            <span><i class="fas fa-fw fa-database"></i> <span>Dadus Sistema</span></span>

        </a>
        <div class="collapse <?php echo $dadus_open ? 'show' : ''; ?>" id="collapseDadus">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $current_page == 'alternatives' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>alternatives">
                    <i class="fas fa-users fa-fw mr-1"></i> Alternativu</a>
                <a class="collapse-item <?php echo $current_page == 'criteria' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>criteria">
                    <i class="fas fa-list-ul fa-fw mr-1"></i> Kriteria</a>
                <a class="collapse-item <?php echo $current_page == 'evaluations' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>evaluations">
                    <i class="fas fa-edit fa-fw mr-1"></i> Avaliasaun</a>
                <a class="collapse-item <?php echo $current_page == 'cripsi' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>script">
                    <i class="fas fa-file-invoice fa-fw mr-1"></i> Script</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Collapsible: Kalkulasaun ARAS -->
    <?php
    $aras_pages = ['normalization', 'matrix', 'si-ki', 'ranking'];
    $aras_open = in_array($current_page, $aras_pages);
    ?>
    <li class="nav-item">
        <a class="nav-link collapsed d-flex justify-content-between align-items-center"
            href="#collapseAras" data-toggle="collapse" aria-expanded="<?php echo $aras_open ? 'true' : 'false'; ?>">
            <span><i class="fas fa-fw fa-calculator"></i> <span>Kalkulasaun ARAS</span></span>

        </a>
        <div class="collapse <?php echo $aras_open ? 'show' : ''; ?>" id="collapseAras">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $current_page == 'normalization' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>normalization">
                    <i class="fas fa-sync fa-fw mr-1"></i>Normalizasaun</a>
                <a class="collapse-item <?php echo $current_page == 'matrix' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>matrix">
                    <i class="fas fa-table fa-fw mr-1"></i>Matris Kalkulasaun</a>
                <a class="collapse-item <?php echo $current_page == 'si-ki' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>si-ki">
                    <i class="fas fa-calculator fa-fw mr-1"></i> Valór Si & Ki</a>
                <a class="collapse-item <?php echo $current_page == 'ranking' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>ranking">
                    <i class="fas fa-trophy fa-fw mr-1"></i> Ranking</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Collapsible: Relatoriu -->
    <?php
    $report_pages = ['reports', 'report_alternatives', 'report_criteria', 'report_ranking'];
    $report_open = in_array($current_page, $report_pages);
    ?>
    <li class="nav-item">
        <a class="nav-link collapsed d-flex justify-content-between align-items-center"
            href="#collapseRelatorio" data-toggle="collapse" aria-expanded="<?php echo $report_open ? 'true' : 'false'; ?>">
            <span><i class="fas fa-fw fa-file-alt"></i> <span>Relatoriu</span></span>

        </a>
        <div class="collapse <?php echo $report_open ? 'show' : ''; ?>" id="collapseRelatorio">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item <?php echo $current_page == 'reports' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>reports">
                    <i class="fas fa-file-invoice fa-fw mr-1"></i>R. Geral</a>
                <a class="collapse-item <?php echo $current_page == 'report_alternatives' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>report-alternatives">
                    <i class="fas fa-users fa-fw mr-1"></i>R. Alternativu</a>
                <a class="collapse-item <?php echo $current_page == 'report_criteria' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>report-criteria">
                    <i class="fas fa-list-ul fa-fw mr-1"></i>R. Kriteria</a>
                <a class="collapse-item <?php echo $current_page == 'report_ranking' ? 'active' : ''; ?>" href="<?php echo $to_pages; ?>report-ranking">
                    <i class="fas fa-trophy fa-fw mr-1"></i>R. Ranking</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['full_name']; ?></span>
                        <?php
                        $top_foto = !empty($_SESSION['foto']) ? $base . 'assets/images/users/' . $_SESSION['foto'] : 'https://ui-avatars.com/api/?name=' . $_SESSION['full_name'] . '&background=4f46e5&color=fff';
                        ?>
                        <img class="img-profile rounded-circle" src="<?php echo $top_foto; ?>" style="object-fit: cover;">
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?php echo $base; ?>logout" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">