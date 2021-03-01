<?php
function getGitBranch()
{
    $shellOutput = [];
    exec('git branch | ' . "grep ' * '", $shellOutput);
    foreach ($shellOutput as $line) {
        if (strpos($line, '* ') !== false) {
            return trim(str_replace('* ', '', $line));
        }
    }
    return null;
}

function format_price($price)
{
    return '￥' . number_format($price);
}

function h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function bulletin_board_pin($bulletin_board_id, $card_flush)
{
    require __DIR__ . '/dbconnect.php';
    $query = "SELECT bulletin_board_contents.bulletin_board_id, bulletin_board_contents.datetime AS edited, bulletin_boards.title, bulletin_boards.status, bulletin_board_contents.content, bulletin_boards.status, CONCAT(profiles.grade, profiles.part, ' ', profiles.last_name, profiles.first_name) as name, bulletin_boards.user_id, hashtags FROM bulletin_board_contents INNER JOIN (SELECT bulletin_board_id, MAX(datetime) as datetime FROM bulletin_board_contents GROUP BY bulletin_board_id) AS T1 ON T1.bulletin_board_id=bulletin_board_contents.bulletin_board_id AND T1.datetime=bulletin_board_contents.datetime INNER JOIN bulletin_boards ON bulletin_board_contents.bulletin_board_id=bulletin_boards.bulletin_board_id INNER JOIN profiles ON bulletin_boards.user_id=profiles.user_id LEFT OUTER JOIN (SELECT bulletin_board_id, GROUP_CONCAT(hashtag SEPARATOR ' ') AS hashtags FROM bulletin_board_hashtags GROUP BY bulletin_board_id) AS bulletin_board_hastags ON bulletin_board_hastags.bulletin_board_id=bulletin_boards.bulletin_board_id WHERE bulletin_boards.bulletin_board_id=$bulletin_board_id";
    $result = $mysqli->query($query);
    if (!$result) {
        print('Query Failed : ' . $mysqli->error);
        $mysqli->close();
        exit();
    }
    if ($result->num_rows == 0) {
        return;
    }
    while ($row = $result->fetch_assoc()) {
        $markdown = $row['content'];
        $title = $row['title'];
        $user_id = $row['user_id'];
        $status = $row['status'];
    }
    $hashtags = explode(" ", $hashtags);

    if ($status == 'DRAFT' && $user_id != $USER->id) {
        return;
    }

    require __DIR__ . '/../vendor/autoload.php';
    $Parsedown = new ParsedownExtra();
    $Parsedown->setBreaksEnabled(true);
    $Parsedown->setSafeMode(true);
    $content = $Parsedown->text($markdown);
?>
    <div class="card <?= $card_flush ?> shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
        </div>
        <div class="card-body">
            <div class="markdown-body text-secondary">
                <?= $content ?>
            </div>
        </div>
    </div>
<?php
}
