<?php
    session_start();
    require_once '../core/common.php';

    $login = $_POST['login'];
    $password = md5($_POST['password']);

    $check_user = mysqli_query($conn, "SELECT * FROM `users` WHERE `login` = '$login' AND `password` = '$password'");
    if (mysqli_num_rows($check_user) > 0) {

        $user = mysqli_fetch_assoc($check_user);

        $_SESSION['user'] = [
            "id" => $user['id'],
            "login" => $user['login'],
            "photo" => $user['photo'],
            "email" => $user['email']
        ];

        header('Location: /');

    } else {
        $_SESSION['message'] = 'Не верный логин или пароль';
        header('Location: /login');
    }
    ?>
