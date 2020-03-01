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

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">電子チケット</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <div class="mb-4">
                <table id="TicketList" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-nowrap">チケット</th>
                            <th class="text-nowrap">発券</th>
                            <th class="text-nowrap">一覧</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM ticket_list ORDER BY date DESC";
                        $result = $mysqli->query($query);
                        if (!$result) {
                            print('Query Failed : ' . $mysqli->error);
                            $mysqli->close();
                            exit();
                        }
                        while ($row = $result->fetch_assoc()) {
                            $ticket_list = new Ticket_List($row);
                            echo '<tr>';
                            echo '<td class="text-nowrap">' . $ticket_list->name . '</td>';
                            echo '<td class="text-nowrap"><a href="./tickets/issue_ticket.php?list_id=' . $ticket_list->id . '">発券</a></td>';
                            echo '<td class="text-nowrap"><a href="./tickets/?list_id=' . $ticket_list->id . '">一覧</a></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a class="btn btn-primary mb-4" href="./create_ticket_list/" role="button">チケット作成</a>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>

<?php
$script = '<script>';
$script .= '$(document).ready(function() {
    $("#TicketList").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Japanese.json"
        },
        order: [], // 初期表示時には並び替えをしない
        lengthMenu: [[ 25, 50, 100, -1 ],[25, 50, 100, "全件"]],
        // columnDefs: [{ "orderable": false, "targets": 0 }],
        deferRender : false,
        autowidth: false,
        scrollX: true,
        // fixedHeader: true
         dom:"<\'row\'<\'col-sm-6\'l><\'col-sm-6 right\'f>>" +
            "<\'row\'<\'col-sm-12 mb-2\'tr>>" +
            "<\'row\'<\'col-sm-6\'i><\'col-sm-6\'p>>"
    }); 
});';
$script .= '</script>';


?>



<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
