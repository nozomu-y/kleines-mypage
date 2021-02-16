<?php
require __DIR__ . '/../../Common/init_page.php';

$PAGE_NAME = "プロフィール設定";
include_once __DIR__ . '/../../Common/head.php';
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">プロフィール設定</h1>
    <div class="row">
        <div class=" col-xl-6 col-sm-12">
            <p>氏名・学年・パートを編集したい場合は管理人までお問い合わせください。</p>
            <form method="post" action="./edit.php" class="mb-4">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="last_name">姓</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="<?= $USER->last_name ?>" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label for="first_name">名</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="<?= $USER->first_name ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name_kana">カナ</label>
                    <input type="text" class="form-control" name="name_kana" id="name_kana" value="<?= $USER->kana ?>" readonly>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="grade">学年</label>
                            <input type="number" class="form-control" name="grade" id="grade" value="<?= $USER->grade ?>" readonly>
                        </div>
                        <?php
                        $sop = '';
                        $alt = '';
                        $ten = '';
                        $bas = '';
                        if ($USER->part == 'S') {
                            $sop = 'selected';
                        } elseif ($USER->part == 'A') {
                            $alt = 'selected';
                        } elseif ($USER->part == 'T') {
                            $ten = 'selected';
                        } elseif ($USER->part == 'B') {
                            $bas = 'selected';
                        }
                        ?>
                        <div class="col-sm-6">
                            <label for="part">パート</label>
                            <input type="text" class="form-control" name="part" id="part" value="<?= $USER->get_part() ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?= $USER->email ?>" required>
                </div>
                <?php
                $admin = '一般';
                $query = "SELECT * FROM admins WHERE user_id=$USER->id";
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
                    } elseif ($row['role'] == 'MANAGER') {
                        $admin = '運営';
                    } elseif ($row['role'] == 'ACCOUNTANT') {
                        $admin = '会計';
                    } elseif ($row['role'] == 'CAMP') {
                        $admin = '合宿委員';
                    }
                }
                ?>
                <div class="form-group">
                    <label for="admin">アカウント権限</label>
                    <input type="text" name="admin" class="form-control" id="admin" value="<?= $admin ?>" readonly>
                </div>
                <input type="hidden" name="user_id" value="<?= $USER->id ?>">
                <button type="submit" class="btn btn-primary" name="submit">更新</button>
                <a class="btn btn-secondary" href="../../" role="button">キャンセル</a>
            </form>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../../Common/foot.php';
