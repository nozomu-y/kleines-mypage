<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->isManager() || $USER->isAccountant())) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/');
    exit();
}
$account = new User($user_id);

if ($USER->isMaster()) {
    $tag = 'required';
} else {
    $tag = 'readonly';
}

$PAGE_NAME = "アカウント管理";
include_once __DIR__ . '/../../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class=" col-xl-6 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">アカウント管理</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $account->name; ?></li>
                </ol>
            </nav>
            <p>氏名・学年・アカウント権限の編集は管理人のみが行えます。</p>
            <form method="post" action="./edit.php" class="mb-4">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="last_name">姓</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="<?= $account->last_name ?>" <?= $tag ?>>
                        </div>
                        <div class="col-sm-6">
                            <label for="first_name">名</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="<?= $account->first_name ?>" <?= $tag ?>>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name_kana">カナ</label>
                    <input type="text" class="form-control" name="name_kana" id="name_kana" value="<?= $account->kana ?>" <?= $tag ?>>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="grade">学年</label>
                            <input type="number" class="form-control" name="grade" id="grade" value="<?= $account->grade ?>" <?= $tag ?>>
                        </div>
                        <?php
                        $sop = '';
                        $alt = '';
                        $ten = '';
                        $bas = '';
                        if ($account->part == 'S') {
                            $sop = 'selected';
                        } elseif ($account->part == 'A') {
                            $alt = 'selected';
                        } elseif ($account->part == 'T') {
                            $ten = 'selected';
                        } elseif ($account->part == 'B') {
                            $bas = 'selected';
                        }
                        ?>
                        <div class="col-sm-6">
                            <label for="part">パート</label>
                            <select class="form-control" id="part" name="part">
                                <option value="S" <?= $sop ?>>Soprano</option>
                                <option value="A" <?= $alt ?>>Alto</option>
                                <option value="T" <?= $ten ?>>Tenor</option>
                                <option value="B" <?= $bas ?>>Bass</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?= $account->email ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="text" name="password" class="form-control" id="password" value="<?= $account->get_password() ?>" readonly>
                </div>
                <?php
                $admin = '一般';
                $master = '';
                $manager = '';
                $accountant = '';
                $camp = '';
                $general = 'selected';
                $query = "SELECT * FROM admins WHERE user_id=$account->id";
                $result = $mysqli->query($query);
                if (!$result) {
                    print('Query Failed : ' . $mysqli->error);
                    $mysqli->close();
                    exit();
                }
                while ($row = $result->fetch_assoc()) {
                    $general = '';
                    if ($row['role'] == 'MASTER') {
                        $admin = '管理人';
                        $master = 'selected';
                    } elseif ($row['role'] == 'MANAGER') {
                        $admin = '運営';
                        $manager = 'selected';
                    } elseif ($row['role'] == 'ACCOUNTANT') {
                        $admin = '会計';
                        $accountant = 'selected';
                    } elseif ($row['role'] == 'CAMP') {
                        $admin = '合宿委員';
                        $camp = 'selected';
                    }
                }
                if ($USER->isMaster()) {
                ?>
                    <div class="form-group">
                        <label for="admin">アカウント権限</label>
                        <select class="form-control" id="admin" name="admin" required>
                            <option value="general" <?= $general ?>>一般</option>
                            <option value="master" <?= $master ?>>管理人</option>
                            <option value="manager" <?= $manager ?>>運営</option>
                            <option value="accountant" <?= $accountant ?>>会計</option>
                            <option value="camp" <?= $camp ?>>合宿委員</option>
                        </select>
                    </div>
                <?php
                } else {
                ?>
                    <div class="form-group">
                        <label for="admin">アカウント権限</label>
                        <input type="text" name="admin" class="form-control" id="admin" value="<?= $admin ?>" readonly>
                    </div>
                <?php
                }
                ?>
                <input type="hidden" name="user_id" value="<?= $account->id ?>">
                <button type="submit" class="btn btn-primary" name="submit">更新</button>
                <a class="btn btn-secondary" href="../" role="button">キャンセル</a>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../../../Common/foot.php';
