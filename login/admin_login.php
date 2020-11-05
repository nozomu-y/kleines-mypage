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

if (!$maintenance) {
    header('Location: ' . MYPAGE_ROOT . '/login');
    exit();
}

if (isset($_SESSION['mypage_auth_error'])) {
    if ($_SESSION['mypage_auth_error'] == "wrong-email") {
        $email_invalid = 'is-invalid';
        $email_message = "メールアドレスが登録されていません";
    } else if (strpos($_SESSION['mypage_auth_error'], "wrong-password") !== false) {
        echo $_SESSION['mypage_auth_error'];
        $login_failure = explode("_", $_SESSION['mypage_auth_error'])[1];
        $password_invalid = 'is-invalid';
        $password_message = "パスワードが間違っています。";
        $failure_message = "ログインに" . $login_failure . "回失敗しています。10回失敗するとアカウントがロックされます。";
    } else if ($_SESSION['mypage_auth_error'] == "login-failure") {
        $email_invalid = 'is-invalid';
        $password_invalid = 'is-invalid';
        $failure_message = "ログインに10回連続で失敗しています。パスワードをリセットしてください。";
    } else if ($_SESSION['mypage_auth_error'] == "unauthorized") {
        $failure_message = "アクセス権限がありません。";
    }
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
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/ck-sb-admin-2.css">
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
                                        <h1 class="h4 text-gray-900 mb-3">Kleines Mypage</h1>
                                        <h2 class="h6 text-gray-800 mb-3">管理者ログイン</h2>
                                    </div>
                                    <form class="user" method="POST" action="./admin_auth.php">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user <?php echo $email_invalid ?>" id="email" name="email" required autocomplete="email" placeholder="メールアドレス">
                                            <span class="invalid-feedback" role="alert">
                                                <?php echo $email_message; ?>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php echo $password_invalid ?>" id="password" placeholder="パスワード" name="password" required autocomplete="current-password">
                                            <span class="invalid-feedback" role="alert">
                                                <?php echo $password_message; ?>
                                            </span>
                                            <span class="invalid-feedback" role="alert">
                                                <?php echo $failure_message; ?>
                                            </span>
                                        </div>
                                        <!-- <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" name="remember_me" id="remember_me" value="checked">
                                                <label class="custom-control-label" for="remember_me">ログイン状態を保持する</label>
                                            </div>
                                        </div> -->
                                        <button type="submit" class="btn btn-primary btn-user btn-block" name="login">
                                            ログイン
                                        </button>
                                    </form>
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