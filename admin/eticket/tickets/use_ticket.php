<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1)) {
    header('Location: /member/mypage/');
    exit();
}

if (!isset($_POST['data'])) {
    header('Location: index.php');
    exit();
}
$data = $_POST['data'];



include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">電子チケット</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <?php
            if (strpos($data, "https://www.chorkleines.com/member/mypage/admin/eticket/redirect.php?list_id=") === false) {
                echo "<p>QRコードが不正です</p>";
                exit();
            }

            $data = explode("redirect.php?list_id=", $data)[1];

            $list_id = substr($data, 0, 5);
            $ticket_id = substr($data, 5, 6);
            $token = substr($data, 11);

            $query = "SELECT * FROM ticket_list WHERE list_id = '$list_id'";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            while ($row = $result->fetch_assoc()) {
                $ticket_list = new Ticket_List($row);
            }

            $query = "SELECT * FROM ticket_$list_id WHERE id = $ticket_id";
            $result = $mysqli->query($query);
            if (!$result) {
                print('Query Failed : ' . $mysqli->error);
                $mysqli->close();
                exit();
            }
            while ($row = $result->fetch_assoc()) {
                $ticket = new Ticket($row);
            }

            if ($token != $ticket->token) {
                echo "<p>チケットが不正です</p>";
                exit();
            }
            echo $ticket_list->name;
            echo '<br>';
            echo $ticket->id;
            ?>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
