<?php
ob_start();
session_start();

require __DIR__.'/../Common/dbconnect.php';
require __DIR__ . '/../Class/User.php';
require __DIR__ .'/../Common/function.php';

if (strcmp(getGitBranch(), "master") && WEB_DOMAIN == "chorkleines.com") {  // if current branch is not master
    $maintenance = true;
} else {
    $maintenance = false;
}

if (isset($_SESSION['mypage_email']) && !$maintenance) {
    header('Location: '.MYPAGE_ROOT);
    exit();
}

if (isset($_SESSION['mypage_auth_error'])) {
    if ($_SESSION['mypage_auth_error'] == "wrong-email") {
        $email_invalid = 'is-invalid';
        $email_message = "メールアドレスが登録されていません";
    } elseif (strpos($_SESSION['mypage_auth_error'], "wrong-password") !== false) {
        echo $_SESSION['mypage_auth_error'];
        $login_failure = explode("_", $_SESSION['mypage_auth_error'])[1];
        $password_invalid = 'is-invalid';
        $password_message = "パスワードが間違っています。";
        $failure_message = "ログインに" . $login_failure . "回失敗しています。10回失敗するとアカウントがロックされます。";
    } elseif ($_SESSION['mypage_auth_error'] == "login-failure") {
        $email_invalid = 'is-invalid';
        $password_invalid = 'is-invalid';
        $failure_message = "ログインに10回連続で失敗しています。パスワードをリセットしてください。";
    }
}
// if password was set properly at /signup/check_password.php
if (isset($_SESSION['mypage_password_success'])) {
    $mypage_password_success = true;
}
$_SESSION = array();
setcookie(session_name(), '', time() - 1, '/');
session_destroy();

if (isset($_COOKIE['mypage_auto_login'])) {
    $token = $_COOKIE['mypage_auto_login'];
    $query = "SELECT * FROM auto_login WHERE token = '$token'";
    $result = $mysqli->query($query);
    if (!$result) {
        print("Query Failed : " . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $row_cnt = $result->num_rows;
    // if the token exists in database
    if ($row_cnt == 1) {
        while ($row = $result->fetch_assoc()) {
            $user_id = $row['id'];
        }
        // get user info
        $query = "SELECT * FROM members WHERE id='$user_id'";
        $result = $mysqli->query($query);
        if (!$result) {
            print("Query Failed : " . $mysqli->error);
            $mysqli->close();
            exit();
        }
        $user = new User($result->fetch_assoc());
        // delete token from database
        $query = "DELETE FROM auto_login WHERE token = '$token'";
        $result = $mysqli->query($query);
        if (!$result) {
            print("Query Failed : " . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // delete token from browser cookie
        setcookie("mypage_auto_login", "", time() - 60);
        // regenerate token
        $token = sha1(uniqid(rand(), true) . mt_rand(1, 999999999) . '_mypage_auto_login');
        // expiration time
        $expiration_time = 3600 * 24 * 30; // token valid for 30 days
        // set cookie
        setcookie("mypage_auto_login", $token, time() + $expiration_time, MYPAGE_ROOT, WEB_DOMAIN, false, true);
        // check device(platform) and browser
        require '../vendor/autoload.php';
        $ua_info = parse_user_agent();
        // check device
        $browser = $ua_info['browser'];
        $device = $ua_info['platform'];
        // add to database
        $query = "INSERT INTO auto_login (id, token, datetime, device, browser) VALUES ('$user->id', '$token', now(), '$device', '$browser')";
        $result = $mysqli->query($query);
        if (!$result) {
            print("Query Failed : " . $mysqli->error);
            $mysqli->close();
            exit();
        }
        // login the user
        if ($user->status != 2) { // if the user status is not resigned
            // start session
            ob_start();
            session_start();
            $_SESSION['mypage_email'] = $user->email;
            // create log
            error_log("[" . date('Y/m/d H:i:s') . "] " . $user->name . " logged in using remember me. \n", 3, __DIR__."/../Core/auth.log");
            header('Location: '.MYPAGE_ROOT);
            exit();
        }
    }
}


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
    <link rel="stylesheet" href="<?=MYPAGE_ROOT?>/Resources/css/ck-sb-admin-2.css">
    <!-- JS -->
    <link rel="stylesheet" href="<?=MYPAGE_ROOT?>/Resources/js/sb-admin-2.min.js">
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
                                        <h1 class="h4 text-gray-900 mb-4">Kleines Mypage</h1>
                                    </div>
                                    <?php
                                    if (!$maintenance) {
                                    ?>
                                        <form class="user" method="POST" action="./auth.php">
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
                                        <hr>
                                        <div class="text-center">
                                        <a class="small" href="<?=MYPAGE_ROOT?>/signup/">パスワードの発行</a>
                                        </div>
                                    <?php
                                    } else { // maintenance mode
                                    ?>
                                        <div class="text-center">
                                            <p>
                                                現在メンテナンス中です。
                                                <br>
                                                Web管までご連絡ください。
                                            </p>
                                            <p>
                                                <a href="./admin_login.php">管理者ログイン</a>
                                            </p>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if ($mypage_password_success) {
                                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                                        echo 'パスワードの設定が完了しました。';
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
    <script src="<?=MYPAGE_ROOT?>/Resources/js/jquery.min.js"></script>
    <script src="<?=MYPAGE_ROOT?>/Resources/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?=MYPAGE_ROOT?>/Resources/js/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?=MYPAGE_ROOT?>/Resources/js/sb-admin-2.min.js"></script>

</body>

</html>
