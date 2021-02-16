<?php
require __DIR__ . '/../../Common/init_page.php';

if (isset($_SESSION['mypage_password_error'])) {
    $password_invalid = 'is-invalid';
    $password_message = "パスワードが一致しません。";
    unset($_SESSION['mypage_password_error']);
}
if (isset($_SESSION['mypage_password_regex_error'])) {
    $password_invalid = 'is-invalid';
    $password_message = "パスワードの形式が異なります。";
    unset($_SESSION['mypage_password_regex_error']);
}
if (isset($_SESSION['mypage_auth_error'])) {
    $auth_invalid = 'is-invalid';
    $auth_message = "パスワードが間違っています。";
    unset($_SESSION['mypage_auth_error']);
}

$PAGE_NAME = "パスワード更新";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">パスワード更新</h1>
    <div class="row">
        <div class=" col-xl-6 col-sm-12">
            <?php
            if (isset($_SESSION['mypage_password_success'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo 'パスワードを更新しました。';
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                echo '</div>';
                unset($_SESSION['mypage_password_success']);
            }
            ?>
            <form method="post" action="./password_update.php" class="mb-4">
                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="old_password">現在のパスワード</label>
                            <input type="password" name="old_password" class="form-control <?= $auth_invalid ?>" id="old_password" pattern="^([\x21-\x7E]{8,})$" autocomplete="current-password" required>
                            <span class="invalid-feedback" role="alert">
                                <?= $auth_message ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="new_password_1">新しいパスワード（大小英文字・数字・記号からなる8文字以上の文字列）</label>
                            <input type="password" name="new_password_1" class="form-control <?= $password_invalid ?>" id="new_password_1" pattern="^([\x21-\x7E]{8,})$" placeholder="パスワード" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_password_2" class="form-control <?= $password_invalid ?>" id="new_password_2" pattern="^([\x21-\x7E]{8,})$" placeholder="パスワード（再入力）" required>
                            <span class="invalid-feedback" role="alert">
                                <?= $password_message ?>
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary" name="submit">更新</button>
                        <a class="btn btn-secondary" href="../../" role="button">キャンセル</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../../Common/foot.php';
