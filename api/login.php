<?php
$json = file_get_contents("php://input");
$contents = json_decode($json, true);

ob_start();
var_dump($contents);
$dump = ob_get_contents();
ob_end_clean();

file_put_contents('./log.txt', $dump);

if (isset($_POST['email'])) {
    file_put_contents('./log.txt', $_POST['email']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $query = "SELECT * FROM members WHERE email='$email' AND status != 2";
    $result = $mysqli->query($query);
    if (!$result) {
        print("Query Failed : " . $mysqli->error);
        $mysqli->close();
        exit();
    }
    $user = new User($result->fetch_assoc());
    $result->close();
    if (password_verify($password, $user->password)) {
        echo 'OK';
    }
}
