<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login.php');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());
$result->close();

if ($PAGE_NAME != "") {
    $PAGE_NAME = " - " . $PAGE_NAME;
}

// include の呼び出し元
$backtrace = debug_backtrace()[0]['file'];
$backtrace = explode('mypage', $backtrace)[1];
if ($backtrace == '/index.php') {
    $home = 'active';
} else if (strpos($backtrace, '/admin/account_manage/') !== false) {
    $account_manage = 'active';
} else if (strpos($backtrace, 'accounts/') !== false) {
    $accounts = 'active';
} else if (strpos($backtrace, '/accounting/collection/') !== false) {
    $accounting_collection = 'active';
} else if (strpos($backtrace, '/accounting/individual/') !== false) {
    $accounting_individual = 'active';
} else if (strpos($backtrace, '/admin/accounting/') !== false) {
    $admin_accounting = 'active';
} else if (strpos($backtrace, '/admin/individual_accounting/') !== false) {
    $admin_accounting = 'active';
} else if (strpos($backtrace, '/admin/camp_accounting/') !== false) {
    $admin_camp_accounting = 'active';
}
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo "Kleines Mypage" . $PAGE_NAME; ?></title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->
    <link href="https://use.fontawesome.com/releases/v5.12.0/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:400,500&display=swap&subset=japanese" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="/member/mypage/Resources/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="/member/mypage/Resources/css/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/datatables.min.css" />
    <!-- JS -->
    <!-- <link rel="stylesheet" href="/member/mypage/Resources/js/sb-admin-2.min.js"> -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-133192700-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-133192700-4');
    </script>
</head>

<body id="page-top" class="sidebar-toggled">
    <div id="wrapper">
        <!-- sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/member/mypage/">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-music"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kleines Mypage</div>
            </a>
            <hr class="sidebar-divider my-0">
            <!-- nav item -->
            <li class="nav-item <?php echo $home; ?>">
                <a class="nav-link" href="/member/mypage/">
                    <i class="fas fa-home fa-fw"></i>
                    <span>Home</span>
                </a>
            </li>
            <hr class="sidebar-divider my-0">
            <li class="nav-item <?php echo $accounts; ?>">
                <a class="nav-link" href="/member/mypage/accounts/">
                    <i class="fas fa-users fa-fw"></i>
                    <span>アカウント一覧</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <!-- nav-item -->
            <div class="sidebar-heading">
                会計システム
            </div>
            <li class="nav-item <?php echo $accounting_collection; ?>">
                <a class="nav-link" href="/member/mypage/accounting/collection/">
                    <i class="fas fa-yen-sign fa-fw"></i>
                    <span>集金記録</span>
                </a>
            </li>
            <li class="nav-item <?php echo $accounting_individual; ?>">
                <a class="nav-link" href="/member/mypage/accounting/individual/">
                    <i class="fas fa-wallet fa-fw"></i>
                    <span>個別会計</span>
                </a>
            </li>
            <?php
            /* 1 : access to all
             * 2 : access to account edit
             * 3 : acccess to kaikei
             * 4 : access to practice plan
             * 5 : access to gasshuku shuukinn
             * 
            */
            if ($user->admin != NULL) {
                echo '<hr class="sidebar-divider">';
                echo '<div class="sidebar-heading">管理コンソール</div>';
                if ($user->admin == 1 || $user->admin == 2 || $user->admin == 3) {
                    echo '<li class="nav-item ' . $account_manage . '"><a class="nav-link" href="/member/mypage/admin/account_manage/"><i class="fas fa-users-cog fa-fw"></i></i><span>アカウント管理</span></a></li>';
                }
                if ($user->admin == 1 || $user->admin == 3) {
                    echo '<li class="nav-item ' . $admin_accounting . '">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccounting" aria-expanded="true" aria-controls="collapseAccounting">
                    <i class="fas fa-fw fa-coins"></i>
                    <span>会計システム</span>
                    </a>';
                    echo '<div id="collapseAccounting" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="/member/mypage/admin/accounting/">集金リスト</a>
                    <a class="collapse-item" href="/member/mypage/admin/individual_accounting/">個別会計</a>
                    </div>
                    </div>
                    </li>';
                }
                if ($user->admin == 2) {
                    echo '<li class="nav-item ' . $admin_accounting . '"><a class="nav-link" href="/member/mypage/admin/accounting/"><i class="fas fa-coins fa-fw"></i></i><span>会計システム</span></a></li>';
                }
                if ($user->admin == 1 || $user->admin == 5) {
                    echo '<li class="nav-item ' . $admin_camp_accounting . '"><a class="nav-link" href="/member/mypage/admin/camp_accounting/"><i class="fas fa-coins fa-fw"></i></i><span>合宿集金</span></a></li>';
                }
            }

            ?>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- end of sidebar -->

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

                    <!-- Topbar Search -->
                    <!-- <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form> -->

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
                                <!-- <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $user->get_name(); ?></span> -->
                                <span class="mr-2 text-gray-600 small"><?php echo $user->get_name(); ?></span>
                                <i class="fas fa-user fa-fw"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <!-- <a class="dropdown-item" href="/member/mypage/profile/">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    プロフィール
                                </a> -->
                                <!-- <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    設定
                                </a> -->
                                <!-- <div class="dropdown-divider"></div> -->
                                <a class="dropdown-item" href="/member/mypage/logout.php" data-toggle="modal" data-target="#logoutModal" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    ログアウト
                                </a>
                                <form action="/member/mypage/logout.php" method="POST" style="display: none;" id="logout-form">
                                    <input type='hidden' name='logout'>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>