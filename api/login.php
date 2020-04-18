<?php
$json = file_get_contents("php://input");
$contents = json_decode($json, true);

if (isset($contents['email'])) {
    file_put_contents('./log.txt', $contents['email']);
    $email = $mysqli->real_escape_string($contents['email']);
    $password = $contents['password'];
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
