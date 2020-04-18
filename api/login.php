<?php
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
