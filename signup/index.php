<?php
ob_start();
session_start();

require __DIR__ . '/../Common/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ . '/../Common/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $maintenance = true;
} else {
    $maintenance = false;
}

if (isset($_SESSION['mypage_user_id']) && !$maintenance) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}

if ($maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_SESSION['mypage_auth_error'])) {
    if ($_SESSION['mypage_auth_error'] == "wrong-email") {
        $email_invalid = 'is-invalid';
        $email_message = "このメールアドレスは登録されていません";
    } else if ($_SESSION['mypage_auth_error'] == "resigned") {
        $email_invalid = 'is-invalid';
        $email_message = "退団者はログインできません";
    }
}
if (isset($_SESSION['mypage_auth_success'])) {
    $mypage_auth_success = true;
    $email = $_SESSION['mypage_auth_success'];
}
if (isset($_SESSION['mypage_token_expired'])) {
    $mypage_token_expired = true;
}
$_SESSION = array();
setcookie(session_name(), '', time() - 1, '/');
session_destroy();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kleines Mypage - Login</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.12.0/css/all.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/sb-admin-2.min.css">
    <!-- JS -->
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/js/sb-admin-2.min.js">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">メール認証</h1>
                                        <p>メールで個人認証を行い、パスワードを設定します。</p>
                                    </div>
                                    <form class="user" method="POST" action="./create_token.php">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user <?= $email_invalid ?>" id="email" name="email" required autocomplete="email" placeholder="メールアドレス">
                                            <span class="invalid-feedback" role="alert">
                                                <?= $email_message ?>
                                            </span>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block" name="signup">
                                            認証
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?= MYPAGE_ROOT ?>/login/">ログインはこちら</a>
                                    </div>
                                    <?php
                                    if ($mypage_auth_success) {
                                        echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">';
                                        echo h($email) . 'にメールを送信しました。<br>24時間以内にリンクをクリックしてパスワードを設定してください。<br>メールが届かない場合は、<a href="mailto:' . ADMIN_EMAIL . '" class="alert-link">' . ADMIN_EMAIL . '</a>までご連絡ください。';
                                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                        echo '</div>';
                                    }
                                    if ($mypage_token_expired) {
                                        echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">';
                                        echo 'リンクの期限が切れました。再度メール認証を行い、リンクを発行してください。';
                                        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-3"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.min.js"></script>
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/sb-admin-2.min.js"></script>

</body>

</html>