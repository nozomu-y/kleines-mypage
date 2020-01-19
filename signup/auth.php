<?php
ob_start();
session_start();
if (isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');

if (!isset($_GET['token'])) {
    header('Location: /member/mypage/');
    exit();
}

if (isset($_SESSION['mypage_password_error'])) {
    $password_invalid = 'is-invalid';
    $password_message = "パスワードが一致しません。";
}

$token = $_GET["token"];
$query = "SELECT * FROM members WHERE token = '$token'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$row_cnt = $result->num_rows;
if ($row_cnt == 0) {
    $_SESSION['mypage_token_expired'] = "";
    header('Location: /member/mypage/signup/');
    exit();
}
$account = new User($result->fetch_assoc());

$validation_time = strtotime($account->validation_time);
$time_now = strtotime(date("Y-m-d H:i:s"));
if ($time_now - $validation_time > 86400) {
    $_SESSION['mypage_token_expired'] = "";
    header('Location: /member/mypage/signup/');
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
    <link rel="stylesheet" href="/member/mypage/Resources/css/sb-admin-2.min.css">
    <!-- JS -->
    <link rel="stylesheet" href="/member/mypage/Resources/js/sb-admin-2.min.js">
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
                                        <p><?php echo $account->name ?>さん。パスワードを英数字8文字以上で入力してください。</p>
                                    </div>
                                    <form class="user" method="POST" action="./check_password.php">
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php echo $password_invalid ?>" id="password1" name="password1" required pattern="^([a-zA-Z0-9]{8,})$" placeholder="パスワード">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user <?php echo $password_invalid ?>" id="password2" name="password2" required pattern="^([a-zA-Z0-9]{8,})$" placeholder="パスワード（再入力）">
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
                                        <a class="small" href="/member/mypage/login/">ログインはこちら</a>
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
    <script src="/member/mypage/Resources/js/jquery.min.js"></script>
    <script src="/member/mypage/Resources/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/member/mypage/Resources/js/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/member/mypage/Resources/js/sb-admin-2.min.js"></script>

</body>

</html>