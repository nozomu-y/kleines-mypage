<?php
// ob_start();
// session_start();
// if (!isset($_SESSION['mypage_email'])) {
//     header('Location: /member/mypage/login/');
//     exit();
// }

// require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
// $email = $_SESSION['mypage_email'];
// $query = "SELECT * FROM members WHERE email='$email'";
// $result = $mysqli->query($query);
// if (!$result) {
//     print('Query Failed : ' . $mysqli->error);
//     $mysqli->close();
//     exit();
// }
// $user = new User($result->fetch_assoc());

// if (!($user->admin == 1)) {
//     header('Location: /member/mypage/');
//     exit();
// }

// $query = "SELECT * FROM members ORDER BY id ASC";
// $result = $mysqli->query($query);
// if (!$result) {
//     print('Query Failed : ' . $mysqli->error);
//     $mysqli->close();
//     exit();
// }

// while ($row = $result->fetch_assoc()) {
//     $account = new User($row);
//     echo 'begin ' . $account->id . ' ' . $account->get_name() . '<br>';
//     $query = "SELECT * FROM individual_accounting_$account->id";
//     $result_1 = $mysqli->query($query);
//     if (!$result_1) {
//         print('Query Failed : ' . $mysqli->error);
//         $mysqli->close();
//         exit();
//     }
//     $row_cnt = $result_1->num_rows;
//     echo $row_cnt . '<br>';
//     if ($row_cnt != 0) {
//         while ($row_1 = $result_1->fetch_assoc()) {
//             $individual_accounting = new Individual_Accounting($row_1);
//             $query = "SELECT * FROM fee_list WHERE name = '$individual_accounting->name'";
//             $result_2 = $mysqli->query($query);
//             if (!$result_2) {
//                 print('Query Failed : ' . $mysqli->error);
//                 $mysqli->close();
//                 exit();
//             }
//             $row_cnt_2 = $result_2->num_rows;
//             echo $row_cnt_2 . '<br>';
//             if ($row_cnt_2 != 0) {
//                 $fee_list = new Fee_List($result_2->fetch_assoc());
//                 $query = "UPDATE individual_accounting_$account->id SET fee_id = $fee_list->id WHERE name = '$individual_accounting->name'";
//                 $result_2 = $mysqli->query($query);
//                 if (!$result_2) {
//                     print('Query Failed : ' . $mysqli->error);
//                     $mysqli->close();
//                     exit();
//                 }
//                 echo $account->id . ' ' . $account->get_name() . ' ' . $individual_accounting->name . '<br>';
//             }
//         }
//     }
// }
