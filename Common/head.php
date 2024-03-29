<?php
if ($PAGE_NAME != "") {
    $PAGE_NAME = " - " . $PAGE_NAME;
}

// include の呼び出し元
$backtrace = debug_backtrace()[0]['file'];
$backtrace = explode('mypage', $backtrace)[1];
if ($backtrace == '/index.php') {
    $home = 'active';
} elseif (strpos($backtrace, '/admin/account_manage/') !== false) {
    $account_manage = 'active';
} elseif (strpos($backtrace, 'accounts/') !== false) {
    $accounts = 'active';
} elseif (strpos($backtrace, '/accounting/collection/') !== false) {
    $accounting_collection = 'active';
} elseif (strpos($backtrace, '/accounting/individual/') !== false) {
    $accounting_individual = 'active';
} elseif (strpos($backtrace, '/admin/accounting/') !== false) {
    $admin_accounting = 'active';
} elseif (strpos($backtrace, '/admin/individual_accounting/') !== false) {
    $admin_accounting = 'active';
} elseif (strpos($backtrace, '/admin/camp_accounting/') !== false) {
    $admin_camp_accounting = 'active';
} elseif (strpos($backtrace, '/info/') !== false) {
    $info = 'active';
} elseif (strpos($backtrace, '/bulletin_board/') !== false) {
    $bulletin_board = 'active';
}

$CSS_JS_CACHE = '20210227';
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= "Kleines Mypage" . $PAGE_NAME ?></title>

    <!-- Fonts -->
    <link href="https://use.fontawesome.com/releases/v5.12.0/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:400,500&display=swap&subset=japanese" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/sb-admin-2.min.css?cache=<?= $CSS_JS_CACHE ?>">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/themes/prism.min.css">
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/github-markdown.min.css?cache=<?= $CSS_JS_CACHE ?>">
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/tagsinput.min.css?cache=<?= $CSS_JS_CACHE ?>">
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/style.css?cache=<?= $CSS_JS_CACHE ?>">
</head>

<body id="page-top" class="sidebar-toggled">
    <div id="wrapper">
        <!-- sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= MYPAGE_ROOT ?>">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-music"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kleines Mypage</div>
            </a>
            <hr class="sidebar-divider my-0">
            <!-- nav item -->
            <li class="nav-item <?= $home ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>">
                    <i class="fas fa-home fa-fw"></i>
                    <span>Home</span>
                </a>
            </li>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?= $accounts ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>/accounts/">
                    <i class="fas fa-users fa-fw"></i>
                    <span>アカウント一覧</span>
                </a>
            </li>
<!--
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?= $bulletin_board ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>/bulletin_board/">
                    <i class="fas fa-clipboard fa-fw"></i>
                    <span>掲示板</span>
                </a>
            </li>
-->
            <hr class="sidebar-divider">
            <!-- nav-item -->
            <div class="sidebar-heading">
                会計システム
            </div>
            <li class="nav-item <?= $accounting_collection ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>/accounting/collection/">
                    <i class="fas fa-yen-sign fa-fw"></i>
                    <span>集金記録</span>
                </a>
            </li>
            <li class="nav-item <?= $accounting_individual ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>/accounting/individual/">
                    <i class="fas fa-wallet fa-fw"></i>
                    <span>個別会計</span>
                </a>
            </li>
            <?php
            if ($USER->isManager() || $USER->isAccountant() || $USER->isCamp()) {
                echo '<hr class="sidebar-divider">';
                echo '<div class="sidebar-heading">管理コンソール</div>';
                if ($USER->isManager() || $USER->isAccountant()) {
                    echo '<li class="nav-item ' . $account_manage . '"><a class="nav-link" href="' . MYPAGE_ROOT . '/admin/account_manage/"><i class="fas fa-users-cog fa-fw"></i></i><span>アカウント管理</span></a></li>';
                }
                if ($USER->isAccountant()) {
                    echo '<li class="nav-item ' . $admin_accounting . '">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccounting" aria-expanded="true" aria-controls="collapseAccounting">
                    <i class="fas fa-fw fa-coins"></i>
                    <span>会計システム</span>
                    </a>';
                    echo '<div id="collapseAccounting" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="' . MYPAGE_ROOT . '/admin/accounting/">集金リスト</a>
                    <a class="collapse-item" href="' . MYPAGE_ROOT . '/admin/individual_accounting/">個別会計</a>
                    </div>
                    </div>
                    </li>';
                }
                if ($USER->isManager() && !$USER->isAccountant()) {
                    echo '<li class="nav-item ' . $admin_accounting . '"><a class="nav-link" href="' . MYPAGE_ROOT . '/admin/accounting/"><i class="fas fa-coins fa-fw"></i></i><span>会計システム</span></a></li>';
                }
                echo '<li class="nav-item ' . $admin_camp_accounting . '"><a class="nav-link" href="' . MYPAGE_ROOT . '/admin/camp_accounting/"><i class="fas fa-coins fa-fw"></i></i><span>合宿集金</span></a></li>';
            }
            ?>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?= $info ?>">
                <a class="nav-link" href="<?= MYPAGE_ROOT ?>/info/">
                    <i class="fas fa-info-circle fa-fw"></i>
                    <span>Info</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" onclick="sidebarToggle();" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- end of sidebar -->

        <script>
            function sidebarToggle() {
                if (document.getElementById('accordionSidebar').classList.contains('toggled')) {
                    document.cookie = 'MypageSidebarToggle=;path=<?= MYPAGE_ROOT ?>';
                } else {
                    document.cookie = 'MypageSidebarToggle=toggled;path=<?= MYPAGE_ROOT ?>';
                }
            }
            for (var c of document.cookie.split(";")) {
                var cArray = c.split('=');
                if (cArray[0] == 'MypageSidebarToggle') {
                    if (cArray[1] == 'toggled') {
                        document.getElementById('accordionSidebar').classList.add('toggled');
                    } else {
                        document.getElementById('accordionSidebar').classList.remove('toggled');
                    }
                }
            }
        </script>

        <!-- content wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- main content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown no-arrow d-md-none">
                            <span class="text-primary">Kleines Mypage</span>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 text-gray-600 small"><?= h($USER->get_name()) ?></span>
                                <i class="fas fa-user fa-fw"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= MYPAGE_ROOT ?>/profile/edit/">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    プロフィール設定
                                </a>
                                <a class="dropdown-item" href="<?= MYPAGE_ROOT ?>/profile/password_update/">
                                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                                    パスワード更新
                                </a>
                                <a class="dropdown-item" href="<?= MYPAGE_ROOT ?>/profile/login_history.php">
                                    <i class="fas fa-history fa-sm fa-fw mr-2 text-gray-400"></i>
                                    ログイン履歴
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= MYPAGE_ROOT ?>/logout.php" data-toggle="modal" data-target="#logoutModal" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    ログアウト
                                </a>
                                <form action="<?= MYPAGE_ROOT ?>/logout.php" method="POST" style="display: none;" id="logout-form">
                                    <input type='hidden' name='logout'>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
