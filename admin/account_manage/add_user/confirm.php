<?php
require __DIR__ . '/../../../Common/init_page.php';

if (!($USER->admin == 1 || $USER->admin == 2 || $USER->admin == 3)) {
    header('Location: ' . MYPAGE_ROOT);
    exit();
}
$PAGE_NAME = "アカウント管理";
include_once __DIR__ . '/../../../Common/head.php';
if (!isset($_POST['submit'])) {
    header('Location: ' . MYPAGE_ROOT . '/admin/account_manage/add_user/');
    exit();
}
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">アカウント管理</h1>
    <div class="row">
        <div class=" col-xl-12 col-sm-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../">アカウント管理</a></li>
                    <li class="breadcrumb-item"><a href="./">アカウントの追加</a></li>
                    <li class="breadcrumb-item active" aria-current="page">確認</li>
                </ol>
            </nav>
            <div class="mb-4">
                <table id="accountList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">学年</th>
                            <th class="text-nowrap">パート</th>
                            <th class="text-nowrap">姓</th>
                            <th class="text-nowrap">名</th>
                            <th class="text-nowrap">フリガナ</th>
                            <th class="text-nowrap">メールアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $array_csv = array();
                        $lines = explode("\n", $_POST["csv"]);
                        foreach ($lines as $line) {
                            $array_csv[] = str_getcsv($line);
                        }
                        foreach ($array_csv as $line) {
                            if ($line[0] == NULL) continue;
                            $grade = trim($line[0]);
                            $part = trim($line[1]);
                            $last_name = trim($line[2]);
                            $first_name = trim($line[3]);
                            $kana = trim($line[4]);
                            $address = trim($line[5]);
                        ?>
                            <tr>
                                <td class="text-nowrap"><?= $grade ?></td>
                                <td class="text-nowrap"><?= $part ?></td>
                                <td class="text-nowrap"><?= $last_name ?></td>
                                <td class="text-nowrap"><?= $first_name ?></td>
                                <td class="text-nowrap"><?= $kana ?></td>
                                <td class="text-nowrap"><?= $address ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <form method="POST" action="./add_user.php">
                    <input type="hidden" name="csv" value="<?= $_POST['csv'] ?>">
                    <button type="submit" class="btn btn-primary" name="submit">アカウントを追加</button>
                    <a class="btn btn-secondary" href="./" role="button">キャンセル</a>
                </form>
            </div>
        </div>
    </div>
</div>




<?php
include_once __DIR__ . '/../../../Common/foot.php';
