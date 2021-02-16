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

if (isset($_SESSION['mypage_password_error'])) {
    $password_invalid = 'is-invalid';
    $password_message = "パスワードが一致しません。";
}

$token = $_GET["token"];
$query = "SELECT * FROM identity_verifications WHERE token = '$token'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$row_cnt = $result->num_rows;
if ($row_cnt == 0) {
    $_SESSION['mypage_token_expired'] = "";
    header('Location: ' . MYPAGE_ROOT . '/signup');
    exit();
}
$row = $result->fetch_assoc();
$user_id = $row['user_id'];
$validation_time = $row['datetime'];
$USER = new User($user_id);

$validation_time = strtotime($validation_time);
$time_now = strtotime(date("Y-m-d H:i:s"));
if ($time_now - $validation_time > 24 * 60 * 60) {
    $_SESSION['mypage_token_expired'] = "";
    header('Location: ' . MYPAGE_ROOT . '/signup');
    exit();
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
                                        <h1 class="h4 text-gray-900 mb-4">パスワードの設定</h1>
                                        <p><?php echo $USER->get_name() ?>さん。パスワードを入力してください。（大小英文字・数字・記号からなる8文字以上の文字列）</p>
                                    </div>
                                    <form class="user" method="POST" action="./check_password.php">
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php echo $password_invalid ?>" id="password1" name="password1" required pattern="^([\x21-\x7E]{8,})$" placeholder="パスワード">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php echo $password_invalid ?>" id="password2" name="password2" required pattern="^([\x21-\x7E]{8,})$" placeholder="パスワード（再入力）">
                                            <span class="invalid-feedback" role="alert">
                                                <?php echo $password_message; ?>
                                            </span>
                                        </div>
                                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                                        <button type="submit" class="btn btn-primary btn-user btn-block" name="set_password">
                                            パスワードを設定
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?= MYPAGE_ROOT ?>/login/">ログインはこちら</a>
                                    </div>
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